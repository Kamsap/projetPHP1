<?php
if (! function_exists('is_admin')) {
    include('./config.php');
}
include('./header.php');
include('./navbar.php');
?>

<div id="booking" class="section">
    <div class="section-center">
        <div class="container">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <div class="booking-form">
                        <div id="booking-cta">
                            <h3>Page not found</h3>
                            <p>
                                La resource n'existe pas ou a ete supprimee !
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('./footer.php');
?>
