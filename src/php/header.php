<header>
    <div class="light-bar">
        <div class="date">
            <script>
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();

                today = mm + '/' + dd + '/' + yyyy;
                document.write(today);
            </script>
        </div>
    </div>
    <div class="header-elements">
        <h1 style="margin-left: 50px">TPI - FIT FILE VIEWER</h1>
        <h2 class="secondary-text">Par Ylli Fazlija</h2>
    </div>
</header>