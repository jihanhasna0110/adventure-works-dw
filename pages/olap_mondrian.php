<?php
$page_title = "Mondrian OLAP Analysis";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<style>
.mondrian-container {
    position: relative;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    height: 85vh;
    margin: 20px 0;
}
.mondrian-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}
.fullscreen-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 1000;
    background: rgba(102, 126, 234, 0.95);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.fullscreen-btn:hover {
    background: #667eea;
    transform: scale(1.08);
}
.guide-section {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-top: 20px;
}
.guide-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}
.guide-item {
    padding: 20px;
    border-left: 4px solid #667eea;
    background: #f8f9fa;
    border-radius: 8px;
}
.guide-item h5 {
    color: #667eea;
    margin-bottom: 10px;
}
</style>

<div class="container-fluid">
    <!-- Header Minimal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cube text-primary mr-2"></i>Mondrian OLAP
        </h1>
    </div>

    <!-- Mondrian Iframe -->
    <div class="mondrian-container">
        <button class="fullscreen-btn" id="fullscreenBtn" title="Fullscreen">
            <i class="fas fa-expand"></i>
        </button>
        <iframe src="http://localhost:8080/mondrian/index.html" title="Mondrian OLAP"></iframe>
    </div>

    <!-- Quick Guide -->
    <div class="guide-section">
        <h4 class="text-primary mb-4">
            <i class="fas fa-info-circle mr-2"></i>Panduan Cepat JPivot
        </h4>
        <div class="guide-grid">
            <div class="guide-item">
                <h5><i class="fas fa-level-down-alt mr-2"></i>Drill-Down</h5>
                <p>Klik ikon <strong>[+]</strong> pada Year → Quarter → Month</p>
            </div>
            <div class="guide-item" style="border-left-color: #28a745;">
                <h5><i class="fas fa-eye mr-2"></i>Drill-Through</h5>
                <p>Klik ikon <strong>🔍 kaca pembesar</strong> pada angka sales</p>
            </div>
            <div class="guide-item" style="border-left-color: #17a2b8;">
                <h5><i class="fas fa-cube mr-2"></i>Cube Navigator</h5>
                <p>Klik ikon <strong>🧊 kubus</strong> → drag dimensi ke slicer</p>
            </div>
            <div class="guide-item" style="border-left-color: #ffc107;">
                <h5><i class="fas fa-exchange-alt mr-2"></i>Pivot</h5>
                <p>Klik ikon <strong>↔️ swap</strong> tukar rows ↔ columns</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const container = document.querySelector('.mondrian-container');
    
    fullscreenBtn.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            container.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });
    
    document.addEventListener('fullscreenchange', function() {
        const icon = fullscreenBtn.querySelector('i');
        if (document.fullscreenElement) {
            icon.className = 'fas fa-compress';
        } else {
            icon.className = 'fas fa-expand';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
