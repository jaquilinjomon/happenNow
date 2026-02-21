<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "happnnow";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publish_event'])) {
    $title = $_POST['title'];
    $mode = $_POST['mode'];
    $audience = $_POST['audience'];
    $loc = $_POST['loc'];
    $date = $_POST['date'];
    $price = !empty($_POST['price']) ? $_POST['price'] : 0;
    $desc = $_POST['desc'];

    // Set a default image
    $img_url = "https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=500&q=60"; 
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $img_url = $target_file;
        }
    }

    $lat = 9.9765; 
    $lng = 76.4225;

   
$stmt = $conn->prepare("INSERT INTO events (title, mode, audience, location, event_date, price, description, image_path, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssdssdd", $title, $mode, $audience, $loc, $date, $price, $desc, $img_url, $lat, $lng);
    
  if($stmt->execute()) {
    $current_file = basename($_SERVER['PHP_SELF']);
    header("Location: " . $current_file . "?status=success");
    exit(); 
}
     else {
        echo "Error: " . $stmt->error;
    }


$result = $conn->query("SELECT * FROM events ORDER BY id DESC");
$db_events = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $db_events[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "mode" => $row['mode'],
            "audience" => $row['audience'],
            "loc" => $row['location'],
            "date" => $row['event_date'],
            "price" => (string)$row['price'],
            "img" => $row['image_path'],
            "lat" => (float)($row['lat'] ?? 9.93),
            "lng" => (float)($row['lng'] ?? 76.26)
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappnNow - Event Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #fffaf9; }
        .sidebar { width: 280px; background: #ff8577; color: white; padding: 20px; position: fixed; height: 100vh; z-index: 100; }
        .main-content { margin-left: 280px; padding: 30px; min-height: 100vh; }
        .nav-item { padding: 12px 15px; margin: 8px 0; border-radius: 8px; cursor: pointer; transition: 0.3s; background: rgba(255, 255, 255, 0.15); text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        .nav-item:hover { background: rgba(255, 255, 255, 0.3); }
        .event-card { background: white; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #eee; }
        .event-img { width: 100%; height: 180px; object-fit: cover; }
        .badge-info { background: #fff1f0; color: #ff8577; font-size: 10px; font-weight: bold; padding: 4px 8px; border-radius: 4px; }
        .distance-tag { font-size: 10px; color: #94a3b8; font-weight: bold; }
        .hidden { display: none; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 200; backdrop-filter: blur(4px); }
    </style>
</head>
<body>

    <div class="sidebar shadow-xl">
        <div class="flex items-center gap-3 mb-8 pb-4 border-b border-white/20">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center overflow-hidden">
                <img src="happenNow.png" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/150?text=H'">
            </div>
            <h2 class="font-bold text-xl tracking-tight">HAPPEN NOW</h2>
        </div>
        <div class="nav-item font-bold" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Explore Events</div>
        <div class="nav-item font-bold" onclick="toggleModal('postModal')">Post Event</div>
        <div class="nav-item font-bold">View Bookings</div>
    </div>

    <div class="main-content">
        <div id="exploreSection">
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">What's Happening</h1>
                <button onclick="findNearby()" id="nearMeBtn" class="flex items-center gap-2 bg-white text-[#ff8577] border-2 border-[#ff8577] px-4 py-2 rounded-xl font-bold hover:bg-[#ff8577] hover:text-white transition shadow-sm">
                    <span>Near Me</span>
                </button>
            </header>
            
            <div id="eventGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"></div>
        </div>
    </div>

    <div id="postModal" class="modal-overlay hidden">
        <div class="bg-white p-8 rounded-3xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6">Create Event</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="file" name="image" class="w-full p-2 border rounded-xl text-sm">
                <input type="text" name="title" placeholder="Event Name" class="w-full p-3 border rounded-xl" required>
                <div class="grid grid-cols-2 gap-4">
                    <select name="mode" class="p-3 border rounded-xl bg-white"><option>Offline</option><option>Online</option></select>
                    <input type="number" name="price" placeholder="Price" class="p-3 border rounded-xl">
                </div>
                <input type="text" name="audience" placeholder="Audience" class="w-full p-3 border rounded-xl" required>
                <input type="text" name="loc" placeholder="Location" class="w-full p-3 border rounded-xl" required>
                <input type="date" name="date" class="w-full p-3 border rounded-xl" required>
                <textarea name="desc" placeholder="Event Details" class="w-full p-3 border rounded-xl h-24"></textarea>
                <button type="submit" name="publish_event" class="w-full py-4 bg-[#ff8577] text-white font-bold rounded-xl">Publish Event</button>
                <button type="button" onclick="toggleModal('postModal')" class="w-full py-2 text-slate-400">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        const allEvents = <?php echo json_encode($db_events); ?>;

        function renderEvents(data) {
            const grid = document.getElementById('eventGrid');
            if(!data || data.length === 0) {
                grid.innerHTML = '<p class="text-slate-400">No events found.</p>';
                return;
            }
            grid.innerHTML = data.map(e => `
                <div class="event-card">
                    <img src="${e.img}" class="event-img" onerror="this.src='https://via.placeholder.com/500x300?text=Event'">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="badge-info uppercase">${e.mode}</span>
                            ${e.dist ? `<span class="distance-tag">${e.dist} km away</span>` : ''}
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">${e.title}</h3>
                        <p class="text-xs text-slate-500 mt-2 font-semibold uppercase">${e.loc}</p>
                        <div class="flex justify-between items-center mt-4">
                           <p class="text-xs text-slate-400">${e.date}</p>
                           <p class="text-sm font-bold text-slate-800">${parseFloat(e.price) <= 0 ? 'FREE' : 'Rs. ' + e.price}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; 
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        function findNearby() {
            const btn = document.getElementById('nearMeBtn');
            btn.innerText = "Locating...";
            navigator.geolocation.getCurrentPosition((pos) => {
                const uLat = pos.coords.latitude;
                const uLon = pos.coords.longitude;
                const nearby = allEvents.map(e => ({
                    ...e,
                    dist: calculateDistance(uLat, uLon, e.lat, e.lng).toFixed(1)
                })).sort((a, b) => a.dist - b.dist);
                renderEvents(nearby);
                btn.innerText = "Nearby Found";
            }, () => {
                alert("Location access denied.");
                btn.innerText = "Near Me";
            });
        }

        function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }
        renderEvents(allEvents);
    </script>
</body>
</html>