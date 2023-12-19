<?php        
        
    function show_navbar($toproof, $working_domain) {        
        ?>
        <!-- NAV BAR START -->

        <nav class="navbar navbar-expand-lg nav-color navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $working_domain ?>">
                   <img src="assets/window-domain_icon-icons.com_52810.png" alt="" style="width:40px;" class="rounded-pill">  Domain Check - WebUI 
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="true" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <form class="d-flex justify-content-center" action="index.php" method="get">
                        <input type="text" class="form-control me-2 search-color" placeholder="Enter Domain or IP" name="toproof" value="<?php echo $toproof; ?>">
                        <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Choose tool
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="navbarContent">
                            <li><button type="submit" class="dropdown-item" name="action" value="health-check">Health Check</button></li>
                            <li><button type="submit" class="dropdown-item" name="action" value="nslookup">nslookup</button></li>
                            <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dig_choose">dig</button></li>
                            <li><button type="submit" class="dropdown-item" name="action" value="whois">whois</button></li>
                            <li><button type="submit" class="dropdown-item" name="action" value="dnssec">dnssec</button></li>
                            <li><button type="submit" class="dropdown-item" name="action" value="rdns-ptr">rDNS/PTR</button></li>
                            <li><button type="submit" class="dropdown-item" name="action" value="puny">Punyconvert</button></li>
                            <!-- <li><button type="submit" class="dropdown-item" name="action" value="mtr">MTR</button></li> -->
                        </ul>
                        </div>

                        <!-- Modal dig choose -->
                        <div class="modal" id="dig_choose">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">Dig Choose</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <pre>Please select what you would like to check here</pre>

                                    <form class="d-flex justify-content-center" action="index.php" method="get">
                                        <div class="btn-group-vertical">
                                            <li><button type="submit" class="dropdown-item" name="action" value="digA">A Records (IPv4)</button></li>
                                            <li><button type="submit" class="dropdown-item" name="action" value="digAAAA">AAAA Records (IPv6)</button></li>
                                            <li><button type="submit" class="dropdown-item" name="action" value="digTXT">TXT Records</button></li>
                                            <li><button type="submit" class="dropdown-item" name="action" value="digMX">MX Records</button></li>
                                        </div>
                                    </form>
                                </div>

                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                </div>
                                
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </nav>

        <!-- NAV BAR END --> 
    <?php
    }

?>


