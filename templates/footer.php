<style>
    .terminal-footer {
      color: #8E8E93;
      font-size: 0.8rem;
      position: relative;
      width: 100%;
      text-align: center;
      margin-top: 16px;
      padding-bottom: 16px;
    }
    
    .terminal-footer a {
      color: #0066CC;
      text-decoration: none;
    }
    
    .terminal-footer a:hover {
      text-decoration: underline;
    }
</style>

<body class="min-vh-100 d-flex flex-column">
    <!-- NAVIGATION BAR HERE -->

    <main class="flex-grow-1 container">
        <!-- MAIN CONTENT GOES HERE -->
    </main>




<footer>

    <div class="terminal-footer" style="font-size: 0.7rem; line-height: 1.3;">
      © <?=date('Y')?> POLYPARTS. All rights reserved.
	  <span class="d-block">For internal use only. Confidential. • <a href="https://www.facebook.com/hafiz066kgi/" target="_blank">by hafiz066kgi</a></span>
    </div>
</footer>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/assets/js/service-worker.js');
  });
}
</script>
    <script src="/assets/js/scripts.js"></script>
</body>
</html>
