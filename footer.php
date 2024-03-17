<?php
    function footer($impressum, $privacy_policy) {
        $version_number = importVersion();
        $changelog = importChangelog();

        ?>

        <div style="height: 50px;"></div>

        <nav class="navbar fixed-bottom navbar-expand-lg footer-color navbar-dark">
            <div class="container-fluid ms-auto">
                <div class="d-flex ms-auto">
                    <div class="btn-group" role="group">
                        <div class="dropup">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Legal</button>
                            <div class="dropdown-menu">
                                <button type="button" class="btn custom" onclick="location.href='<?php echo $impressum ?>';">Legal notice</button>
                                <button type="button" class="btn custom" onclick="location.href='<?php echo $privacy_policy ?>';">Privacy policy</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#licenses">Licenses</button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changelog"><?php echo $version_number ?></button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#contribute">Contribute</button>
                    </div>
                </div>
            </div>
        </nav>


                    <!-- Modal Licenses -->
                    <div class="modal" id="licenses">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Used licenses</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="card bg-dark">
                            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseLicense" aria-expanded="false" aria-controls="collapseLicense">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseLicense">
                                Domain Check Web UI
                            </a>
                            </div>
                            <div id="collapseLicense" class="collapse show" data-bs-parent="#accordion">
                                <div class="card-body card-body-style">
                                    This work is licenced under a <a rel="license" href="./LICENSE">GNU GENERAL PUBLIC LICENSE</a>.
                                </div>
                            </div>
                        </div>

                        <div class="card bg-dark">
                            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseIcon" aria-expanded="false" aria-controls="collapseIcon">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseIcon">
                                Icon
                            </a>
                            </div>
                            <div id="collapseIcon" class="collapse show" data-bs-parent="#accordion">
                                <div class="card-body card-body-style">
                                    Icon from <a href="https://icon-icons.com/icon/window-domain-www/52810">Icon-Icons.com</a> made by <a href="https://icon-icons.com/users/49oaZ80LDyqHrUI3wINLc/icon-sets/">Vecteezy</a> licensed under <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>.
                                </div>
                            </div>
                        </div>

                        <div class="card bg-dark">
                            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseBoot" aria-expanded="false" aria-controls="collapseBoot">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseBoot">
                                Bootstrap
                            </a>
                            </div>
                            <div id="collapseBoot" class="collapse show" data-bs-parent="#accordion">
                                <div class="card-body card-body-style">
                                    The page uses the Bootstrap framework.
                                    The integration is implemented locally. 
                                    Bootsrap uses the <a rel="license" href="https://raw.githubusercontent.com/twbs/bootstrap/main/LICENSE">MIT license</a>.
                                </div>
                            </div>
                        </div>

                        <div class="card bg-dark">
                            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseBoot" aria-expanded="false" aria-controls="collapseBoot">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseBoot">
                                Feather
                            </a>
                            </div>
                            <div id="collapseBoot" class="collapse show" data-bs-parent="#accordion">
                                <div class="card-body card-body-style">
                                The page uses Feather Icons
                                Feather uses the <a rel="license" href="https://github.com/feathericons/feather/blob/main/LICENSE">MIT license</a>.
                                </div>
                            </div>
                        </div>
                

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                    </div>
                </div>
            </div>


            <!-- Modal Contribute -->
            <div class="modal" id="contribute">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Contribute</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        Together, a project always gets better.<br/>
                        Therefore, do you have a suggestion for improvement or even want to help with the code? 
                        <br/>
                        <br/>
                        Then take a look at the Github page:
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="location.href='https://github.com/Truemmerer/domain_check_web_ui/';">GitHub Page</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>

            <!-- Modal Changelog -->
            <div class="modal" id="changelog">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Version <?php echo $version_number ?></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <h3>Changelog</h3>
                        <p>
                            <?php echo nl2br($changelog) ?>
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="location.href='https://github.com/Truemmerer/domain_check_web_ui/';">GitHub Page</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>
    
       <?php

    }

    function importChangelog() {
        $filename = "Changelog.md";
        return file_get_contents($filename);    
    }

    function importVersion() {
        $filename = "Version.txt";
        return file_get_contents($filename);    
    }

?>