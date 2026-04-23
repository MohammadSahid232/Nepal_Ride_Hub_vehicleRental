<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
$isAdmin = ($_SESSION['role'] === 'admin');
?>

<section style="background: #1a1a1a; color: #fff; min-height: 90vh; display: flex; flex-direction: column;">
    <div style="padding: 1rem 2rem; background: #222; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; color: #fff; font-size: 1.5rem;"><i class="fas fa-satellite-dish" style="color: var(--primary-red);"></i> Live Fleet Tracking</h2>
            <p style="margin: 0; font-size: 0.8rem; color: #888;">Monitoring all active customer trips across Nepal</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div id="connectionStatus" style="font-size: 0.85rem; background: #2d2d2d; padding: 0.5rem 1rem; border-radius: 50px; display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 8px; height: 8px; background: #28a745; border-radius: 50%;"></span> Live
            </div>
            <a href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'customer_dashboard.php'; ?>" class="btn btn-outline" style="border-color: #444; color: #ccc;">Back to Dashboard</a>
        </div>
    </div>

    <div style="display: flex; flex: 1; overflow: hidden;">
        <!-- Trip Sidebar -->
        <div style="width: 350px; background: #222; border-right: 1px solid #333; overflow-y: auto; padding: 1.5rem;">
            <h4 style="margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: #666;">Active Trips</h4>
            <div id="tripList" style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Loaded dynamically -->
                <div style="color: #444; text-align: center; padding: 2rem;">Searching for active trips...</div>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #333;">
                <p style="font-size: 0.75rem; color: #666; margin-bottom: 1rem;">🛠️ Maintenance Tools</p>
                <a href="seed_coordinates.php" target="_blank" class="btn btn-outline btn-block" style="border-color: #444; color: #ccc; font-size: 0.8rem; text-align: left;">
                    <i class="fas fa-sync"></i> Sync Sample GPS Data
                </a>
                <button onclick="loadActiveTrips()" class="btn btn-outline btn-block" style="border-color: #444; color: #ccc; font-size: 0.8rem; text-align: left; margin-top: 0.5rem;">
                    <i class="fas fa-rotate-right"></i> Force Refresh
                </button>
            </div>
        </div>

        <!-- Map Container -->
        <div style="flex: 1; position: relative;">
            <div id="map" style="width: 100%; height: 100%; background: #1a1a1a;"></div>
            
            <!-- Map Overlay -->
            <div style="position: absolute; bottom: 20px; right: 20px; background: rgba(34,34,34,0.9); padding: 1rem; border-radius: 8px; border: 1px solid #444; backdrop-filter: blur(5px);">
                <div style="font-size: 0.75rem; color: #888; margin-bottom: 0.5rem;">FLEET SUMMARY</div>
                <div style="display: flex; gap: 2rem;">
                    <div>
                        <div id="activeCount" style="font-size: 1.5rem; font-weight: 800;">0</div>
                        <div style="font-size: 0.7rem; color: #666;">VEHICLES</div>
                    </div>
                    <div>
                        <div id="movingCount" style="font-size: 1.5rem; font-weight: 800;">0</div>
                        <div style="font-size: 0.7rem; color: #666;">MOVING</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trip Detail Modal (Optional hidden by default) -->
<div id="tripModal" style="display: none; position: fixed; bottom: 20px; left: 370px; width: 300px; background: #fff; color: #111; padding: 1.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
        <h5 id="modalTitle" style="margin: 0;">Vehicle Name</h5>
        <button onclick="closeModal()" style="border: none; background: none; cursor: pointer;"><i class="fas fa-times"></i></button>
    </div>
    <img id="modalImg" src="" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
    <div style="font-size: 0.9rem;">
        <p><strong>Customer:</strong> <span id="modalCustomer">Name</span></p>
        <p><strong>Phone:</strong> <span id="modalPhone">Phone</span></p>
        <p><strong>Status:</strong> <span id="modalStatus" style="color: var(--success);">In Transit</span></p>
    </div>
    <a id="callBtn" href="#" class="btn btn-primary btn-block" style="margin-top: 1rem; padding: 0.6rem; text-decoration: none; text-align: center; display: block;">
        <i class="fas fa-phone"></i> Call Driver
    </a>
</div>

<!-- Leaflet CSS & JS (Free, No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let map;
    let markers = {};
    let activeTrips = [];

    // Initialize Map (Leaflet version)
    function initLeafletMap() {
        // Center on Nepal (Kathmandu area)
        map = L.map('map', {
            zoomControl: false // Move zoom control if needed, but we'll hide for premium look
        }).setView([28.3949, 84.1240], 8);

        // CartoDB Dark Matter (Premium Dark Theme)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        loadActiveTrips();
        setInterval(loadActiveTrips, 15000); // Poll API every 15s
        
        // --- Live Movement Simulation ---
        setInterval(simulateLiveMovement, 2000); // Smooth movement every 2s
    }

    // Call init on load
    document.addEventListener('DOMContentLoaded', initLeafletMap);

    // Simulation Engine: Moves markers slightly to simulate real-time driving
    function simulateLiveMovement() {
        activeTrips.forEach(trip => {
            // Only simulate movement for "Confirmed" (Active) trips
            if (trip.booking_status !== 'confirmed') return;

            // Randomly jitter position slightly (0.0001 degrees is ~10-15 meters)
            const latDiff = (Math.random() - 0.5) * 0.0003;
            const lngDiff = (Math.random() - 0.5) * 0.0003;

            trip.gps_lat = parseFloat(trip.gps_lat) + latDiff;
            trip.gps_lng = parseFloat(trip.gps_lng) + lngDiff;

            // Update marker on map if it exists
            if (markers[trip.vehicle_id]) {
                markers[trip.vehicle_id].setLatLng([trip.gps_lat, trip.gps_lng]);
            }
        });
        // We don't re-render the whole list to keep it smooth, 
        // but we update the text in current trip modal if open
        const modal = document.getElementById('tripModal');
        if (modal.style.display === 'block') {
            // Optionally update coordinates text here if wanted
        }
    }

    async function loadActiveTrips() {
        try {
            const response = await fetch('api/manage_vehicles.php?action=list_active_tracking');
            const data = await response.json();
            
            if (data.success) {
                activeTrips = data.trips;
                renderTripList();
                updateMarkers();
                document.getElementById('activeCount').innerText = activeTrips.length;
                
                // Count moving (for simulation/demo we just count those with status confirmed)
                const moving = activeTrips.filter(t => t.booking_status === 'confirmed').length;
                document.getElementById('movingCount').innerText = moving;
            }
        } catch (e) {
            console.error("Tracking Error:", e);
        }
    }

    function renderTripList() {
        const list = document.getElementById('tripList');
        if (activeTrips.length === 0) {
            list.innerHTML = `
                <div style="color: #444; text-align: center; padding: 2rem;">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                    No vehicles currently in transit.<br>
                    <small style="display: block; margin-top: 1rem; color: #666;">Make sure you have <strong>'pending'</strong> or <strong>'confirmed'</strong> bookings.</small>
                </div>`;
            return;
        }

        list.innerHTML = activeTrips.map(trip => {
            const hasGps = trip.gps_lat && trip.gps_lng;
            const statusColor = trip.booking_status === 'confirmed' ? '#28a745' : '#ffc107';
            
            return `
            <div onclick="${hasGps ? `focusTrip(${trip.booking_id})` : ''}" 
                 style="background: #2d2d2d; padding: 1rem; border-radius: 8px; cursor: pointer; transition: 0.2s; border: 1px solid transparent; margin-bottom: 1rem; opacity: ${hasGps ? 1 : 0.6};" 
                 onmouseover="this.style.borderColor='#444'" onmouseout="this.style.borderColor='transparent'">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <div style="width: 40px; height: 40px; background: #444; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas ${getVehicleIcon(trip.vehicle_type)}" style="color: var(--primary-red);"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 0.9rem;">${trip.vehicle_name}</div>
                        <div style="font-size: 0.7rem; color: #888;">${trip.customer_name}</div>
                    </div>
                    <div style="background: ${hasGps ? statusColor : '#666'}; width: 8px; height: 8px; border-radius: 50%;"></div>
                </div>
                <div style="font-size: 0.7rem; color: #888; display: flex; justify-content: space-between;">
                    <span>
                        ${hasGps 
                            ? `<i class="fas fa-map-marker-alt"></i> ${parseFloat(trip.gps_lat).toFixed(4)}, ${parseFloat(trip.gps_lng).toFixed(4)}` 
                            : `<i class="fas fa-satellite-dish"></i> <span style="color:#d9534f;">SIGNAL LOST</span>`
                        }
                    </span>
                    <span style="color: ${statusColor};">
                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i> ${trip.booking_status.toUpperCase()}
                    </span>
                </div>
            </div>
        `;
        }).join('');
    }

    function updateMarkers() {
        const tripIdsWithGps = activeTrips.filter(t => t.gps_lat && t.gps_lng).map(t => t.vehicle_id.toString());
        
        // Remove dead markers
        Object.keys(markers).forEach(id => {
            if (!tripIdsWithGps.includes(id)) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }
        });

        activeTrips.forEach(trip => {
            if (!trip.gps_lat || !trip.gps_lng) return; // Skip those without GPS

            const pos = [parseFloat(trip.gps_lat), parseFloat(trip.gps_lng)];
            
            if (markers[trip.vehicle_id]) {
                // Leaflet update position
                markers[trip.vehicle_id].setLatLng(pos);
            } else {
                // Create custom icon
                const myIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: var(--primary-red); color: white; border: 2px solid white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                             <i class="fas ${getVehicleIcon(trip.vehicle_type)}" style="font-size: 14px;"></i>
                           </div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                markers[trip.vehicle_id] = L.marker(pos, { icon: myIcon }).addTo(map);
                markers[trip.vehicle_id].on('click', () => {
                    openTripModal(trip);
                });
            }
        });
    }

    function focusTrip(bookingId) {
        const trip = activeTrips.find(t => t.booking_id == bookingId);
        if (trip) {
            map.flyTo([parseFloat(trip.gps_lat), parseFloat(trip.gps_lng)], 14, {
                animate: true,
                duration: 1.5
            });
            openTripModal(trip);
        }
    }

    function openTripModal(trip) {
        const modal = document.getElementById('tripModal');
        document.getElementById('modalTitle').innerText = trip.vehicle_name;
        document.getElementById('modalImg').src = trip.image_path;
        document.getElementById('modalCustomer').innerText = trip.customer_name;
        document.getElementById('modalPhone').innerText = trip.customer_phone;
        
        // Link the call button
        const callBtn = document.getElementById('callBtn');
        callBtn.href = `tel:${trip.customer_phone}`;
        
        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('tripModal').style.display = 'none';
    }

    function getVehicleIcon(type) {
        switch(type) {
            case 'bike': return 'fa-motorcycle';
            case 'bus': return 'fa-bus';
            case 'jeep': return 'fa-truck-pickup';
            default: return 'fa-car';
        }
    }
</script>


<?php include 'includes/footer.php'; ?>
