<?php

function mxExplain() {

    ?>
        An MX record (Mail Exchange) determines where emails are forwarded to. 
        Like CNAME entries, they must point to another domain. 

        <div class="container mt-3">
            <h5>Example</h5>       
            <table class="table table-dark table-borderless">
                <thead>
                <tr>
                    <th>example.com</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Destination</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>@</td>
                    <td>MX</td>
                    <td>10</td>
                    <td>mail01.example.com</td>
                </tr>
                <tr>
                    <td>@</td>
                    <td>MX</td>
                    <td>50</td>
                    <td>mail02.example.com</td>
                </tr>
                </tbody>
            </table>
        </div>
        <p>
            The priority indicates which entry, and therefore which mail server, is preferred. The lowest value is the preferred value.</br>
            In this case, email is first attempted to mail01.example.com. If this fails, the email will be sent to mail02.example.com.</br></br>

            However, the same priority can also be used to spread the load of incoming emails across both mail servers.
        </p>
    <?php
}

function aExplain() {
    ?>
        An A record is the most basic type of DNS.
        It specifies which IPv4 (server) a domain should point to. 
        
        <div class="container mt-3">
            <h5>Example</h5>       
            <table class="table table-dark table-borderless">
                <thead>
                <tr>
                    <th>example.com</th>
                    <th>Type</th>
                    <th>Destination</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>@</td>
                    <td>A</td>
                    <td>192.0.2.1</td>
                </tr>
                </tbody>
            </table>
        </div>
        
        In this example, the domain example.com would point to the server with the IP 192.0.2.1. This can then serve a website, for example. 
    <?php
}

function aaaaExplain() {
    ?>
        An AAAA record is the most basic type of DNS.
        It specifies which IPv6 (server) a domain should point to. 
        
        <div class="container mt-3">
            <h5>Example</h5>       
            <table class="table table-dark table-borderless">
                <thead>
                <tr>
                    <th>example.com</th>
                    <th>Type</th>
                    <th>Destination</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>@</td>
                    <td>AAAA</td>
                    <td>2001:0db8:85a3:0:8a2e:1563:1236</td>
                </tr>
                </tbody>
            </table>
        </div>
        
        In this example, the domain example.com would point to the server with the IP 192.0.2.1. This can then serve a website, for example. 
    <?php
}

function txtExplain() {
    ?>
    A TXT record is a freely definable text field. In theory, anything can be written into this field.</br>
    Today it is mainly used to validate domain ownership and to prevent email spam.
        
        <div class="container mt-3">
            <h5>Example</h5>       
            <table class="table table-dark table-borderless">
                <thead>
                <tr>
                    <th>example.com</th>
                    <th>Type</th>
                    <th>Destination</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>@</td>
                    <td>TXT</td>
                    <td>This is a random text with no purpose</td>
                </tr>
                <tr>
                    <td>@</td>
                    <td>TXT</td>
                    <td>v=spf1 mx a ~all</td>
                </tr>
                <tr>
                    <td>@</td>
                    <td>TXT</td>
                    <td>v=DMARC1; p=none; rua=mailto:dmarc@example.com"</td>
                </tr>
                </tbody>
            </table>
        </div>
        
        In this example there is a TXT email with a random text. Also the SPF and DMARC records that make sense for the mail traffic. 
    <?php
}

function cnameExplain() {
    ?>
    A CNAME (canonical name) is an alias record.<br/>
    Which means: The DNS of another domain is copied with the CNAME record.<br/>
    It is important to note that a CNAME can only be created for a subdomain. 

    <div class="container mt-3">
            <h5>Example</h5>       
            <table class="table table-dark table-borderless">
                <thead>
                <tr>
                    <th>example.com</th>
                    <th>Type</th>
                    <th>Destination</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>www</td>
                    <td>CNAME</td>
                    <td>example.com</td>
                </tr>
                </tbody>
            </table>
        </div>
    
    In this example, the subdomain www.example.com clones the DNS of the domain example.com.
    <div class="alert alert-warning">
        <strong>Attention!</strong> A CNAME is not a forward! Domain to domain forwarding must be handled by a server.
    </div>
     
    <?php
}

function nsExplain() {
    ?>
    NS means name server. This indicates which servers are responsible for distributing the DNS for the domain or subdomain.   
    <div class="container mt-3">
        <h5>Example</h5>       
        <table class="table table-dark table-borderless">
            <thead>
            <tr>
                <th>example.com</th>
                <th>Type</th>
                <th>Destination</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>coolbuster</td>
                <td>NS</td>
                <td>ns1.domain.tld</td>
            </tr>
            </tbody>
        </table>
    </div>

    In this example, the name server ns1.domain.tld is responsible for the DNS for the subdomain coolbuster.example.com. 

    <?php
}

function nsModal() {
    ?>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#nsModal">
            <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
        </button>
    <?php
}

?>