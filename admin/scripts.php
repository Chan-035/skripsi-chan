<!-- Bootstrap core JavaScript-->
    <script src="admin/vendor/jquery/jquery.min.js"></script>
    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="admin/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="admin/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="admin/js/demo/chart-area-demo.js"></script>
    <script src="admin/js/demo/chart-pie-demo.js"></script>

    <script>
function updateJam() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    
    // Array untuk nama hari dan bulan dalam Bahasa Indonesia
    var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Format jam
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    
    // Format tanggal
    var tanggal = now.getDate();
    var hariIni = hari[now.getDay()];
    var bulanIni = bulan[now.getMonth()];
    var tahun = now.getFullYear();
    
    var timeString = hours + ':' + minutes + ':' + seconds;
    var dateString = hariIni + ', ' + tanggal + ' ' + bulanIni + ' ' + tahun;
    
    document.getElementById('jamDigital').innerHTML = timeString;
    document.getElementById('tanggalHariIni').innerHTML = dateString;
}



setInterval(updateJam, 1000);
updateJam();



</script>
