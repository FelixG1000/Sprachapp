</div><!-- Container Ende -->
    
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase"><?= APP_NAME ?></h5>
                    <p>
                        Deine persönliche Sprachapp zum Lernen von Vokabeln mit Karteikarten, 
                        Audio-Unterstützung und verschiedenen Lernmethoden.
                    </p>
                </div>
                
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Links</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a href="about.php" class="text-dark">Über uns</a>
                        </li>
                        <li>
                            <a href="contact.php" class="text-dark">Kontakt</a>
                        </li>
                        <li>
                            <a href="privacy.php" class="text-dark">Datenschutz</a>
                        </li>
                        <li>
                            <a href="imprint.php" class="text-dark">Impressum</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © <?= date('Y') ?> <?= APP_NAME ?>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS mit Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (für AJAX-Anfragen) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Hauptskript -->
    <script src="js/main.js"></script>
</body>
</html>