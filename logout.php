<?php
session_start();
session_destroy();
?>
<script>
localStorage.removeItem("user");
window.location.href = "index.php";
</script>