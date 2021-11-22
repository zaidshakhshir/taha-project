<?php
    require_once '../includes/lb_helper.php'; // Include LicenseBox external/client api helper file
    $api = new LicenseBoxAPI(); // Initialize a new LicenseBoxAPI object
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>MealUp - Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <style>
    #cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:9999;
    display:none;
    }

    @-webkit-keyframes spin {
        from {-webkit-transform:rotate(0deg);}
        to {-webkit-transform:rotate(360deg);}
    }

    @keyframes spin {
        from {transform:rotate(0deg);}
        to {transform:rotate(360deg);}
    }

    #cover-spin::after {
        content:'';
        display:block;
        position:absolute;
        left:48%;top:40%;
        width:40px;height:40px;
        border-style:solid;
        border-color:black;
        border-top-color:transparent;
        border-width: 4px;
        border-radius:50%;
        -webkit-animation: spin .8s linear infinite;
        animation: spin .8s linear infinite;
    }
</style>
</head>

<body>
    <div id="cover-spin"></div>
    <?php
    $errors = false;
    $step = isset($_GET['step']) ? $_GET['step'] : '';
    ?>
    <div class="container">
        <div class="section">
            <div class="column is-6 is-offset-3">
                <center>
                    <h1 class="title" style="padding-top: 20px">MealUp</h1><br>
                </center>
                <div class="box">
                    <?php
                    switch ($step) {
                        default: ?>
                            <div class="tabs is-fullwidth">
                                <ul>
                                    <li class="is-active">
                                        <a>
                                            <span><b>Requirements</b></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Verify</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Database</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Finish</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            // Add or remove your script's requirements below
                            if (phpversion() < "5.5") {
                                $errors = true;
                                echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Current PHP version is " . phpversion() . "! minimum PHP 5.5 or higher required.</div>";
                            } else {
                                echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> You are running PHP version " . phpversion() . "</div>";
                            }
                            if (!extension_loaded('mysqli')) {
                                $errors = true;
                                echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> MySQLi PHP extension missing!</div>";
                            } else {
                                echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> MySQLi PHP extension available</div>";
                            } ?>
                            <div style='text-align: right;'>
                                <?php if ($errors == true) { ?>
                                    <a href="javascript:void(0);" class="button is-link" disabled>Next</a>
                                <?php } else { ?>
                                    <a href="index.php?step=0" class="button is-link">Next</a>
                                <?php } ?>
                            </div>
                            <?php
                                    break;
                                case "0": ?>
                            <div class="tabs is-fullwidth">
                                <ul>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Requirements</span>
                                        </a>
                                    </li>
                                    <li class="is-active">
                                        <a>
                                            <span><b>Verify</b></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Database</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Finish</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                                    $license_code = null;
                                    $client_name = null;
                                    if (!empty($_POST['license']) && !empty($_POST['client']))
                                    {
                                        $license_code = strip_tags(trim($_POST["license"]));
                                        $client_name = strip_tags(trim($_POST["client"]));
                                        /* Once we have the license code and client's name we can use LicenseBoxAPI's activate_license() function for activating/installing the license, if the third parameter is empty a local license file will be created which can be used for background license checks. */
                                        $activate_response = $api->activate_license($license_code, $client_name);
                                        if (empty($activate_response)) {
                                            $msg = 'Server is unavailable.';
                                        } else {
                                            $msg = $activate_response['message'];
                                        }
                                        if ($activate_response['status'] != true) { ?>
                                    <form action="index.php?step=0" method="POST">
                                        <div class="notification is-danger"><?php echo ucfirst($msg); ?></div>
                                        <div class="field">
                                            <input type="hidden" name="license_code" value="<?php echo $license_code ?>">
                                            <input type="hidden" name="client_name" value="<?php echo $client_name ?>">
                                            <label class="label">License code</label>
                                            <div class="control">
                                                <input class="input" type="text" placeholder="enter your purchase/license code" name="license" required>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Your name</label>
                                            <div class="control">
                                                <input class="input" type="text" placeholder="enter your name/envato username" name="client" required>
                                            </div>
                                        </div>
                                        <div style='text-align: right;'>
                                            <button type="submit" class="button is-link">Verify</button>
                                        </div>
                                    </form><?php
                                        } else { ?>
                                    <form action="index.php?step=1" method="POST">
                                        <div class="notification is-success"><?php echo ucfirst($msg); ?></div>
                                        <input type="hidden" name="lcscs" id="lcscs" value="<?php echo ucfirst($activate_response['status']); ?>">
                                        <input type="hidden" name="license_code" value="<?php echo $license_code ?>">
                                        <input type="hidden" name="client_name" value="<?php echo $client_name ?>">
                                        <div style='text-align: right;'>
                                            <button type="submit" class="button is-link">Next</button>
                                        </div>
                                    </form><?php
                                        }
                                    } else { ?>
                                <form action="index.php?step=0" method="POST">
                                    <div class="field">
                                        <label class="label">License code</label>
                                        <div class="control">
                                            <input class="input" type="text" placeholder="enter your purchase/license code" name="license" required>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Your name</label>
                                        <div class="control">
                                            <input class="input" type="text" placeholder="enter your name/envato username" name="client" required>
                                        </div>
                                    </div>
                                    <div style='text-align: right;'>
                                        <button type="submit" class="button is-link">Verify</button>
                                    </div>
                                </form>
                            <?php }
                                    break;
                                case "1": ?>
                            <div class="tabs is-fullwidth">
                                <ul>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Requirements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Verify</span>
                                        </a>
                                    </li>
                                    <li class="is-active">
                                        <a>
                                            <span><b>Database</b></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span>Finish</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                                    if ($_POST && isset($_POST["lcscs"])) {
                                        $valid = strip_tags(trim($_POST["lcscs"]));
                                        $license_code = strip_tags(trim($_POST["license_code"]));
                                        $client_name = strip_tags(trim($_POST["client_name"]));
                                        $db_host = strip_tags(trim($_POST["host"]));
                                        $db_user = strip_tags(trim($_POST["user"]));
                                        $db_pass = strip_tags(trim($_POST["pass"]));
                                        $db_name = strip_tags(trim($_POST["name"]));
                                        // Let's import the sql file into the given database
                                        if (!empty($db_host)) {
                                            $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                                            if (mysqli_connect_errno()) { ?>
                                        <form action="index.php?step=1" method="POST">
                                            <div class='notification is-danger'>Failed to connect to MySQL: <?php echo mysqli_connect_error(); ?></div>
                                            <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                                            <input type="hidden" name="license_code" id="license_code" value="<?php echo $license_code; ?>">
                                            <input type="hidden" name="client_name" id="client_name" value="<?php echo $client_name; ?>">
                                            <div class="field">
                                                <label class="label">Database Host</label>
                                                <div class="control">
                                                    <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Database Username</label>
                                                <div class="control">
                                                    <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Database Password</label>
                                                <div class="control">
                                                    <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Database Name</label>
                                                <div class="control">
                                                    <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                                                </div>
                                            </div>
                                            <div style='text-align: right;'>
                                                <button type="submit" class="button db_import is-link">Import</button>
                                            </div>
                                        </form><?php
                                                exit;
                                            }
                                            $templine = '';
                                            $filename = '../includes/db_mealup.sql';
                                            $lines = file($filename);
                                            foreach ($lines as $line) {
                                                if (substr($line, 0, 2) == '--' || $line == '')
                                                    continue;
                                                $templine .= $line;
                                                $query = false;
                                                if (substr(trim($line), -1, 1) == ';') {
                                                    $query = mysqli_query($con, $templine);
                                                    $templine = '';
                                                }
                                            } ?>
                                    <form action="index.php?step=2" method="POST">
                                        <div class='notification is-success'>Database was successfully imported.</div>
                                        <input type="hidden" name="dbscs" id="dbscs" value="true">
                                        <input type="hidden" name="license_code" id="license_code" value="<?php echo $license_code; ?>">
                                        <input type="hidden" name="client_name" id="client_name" value="<?php echo $client_name; ?>">
                                        <input type="hidden" name="db_host" id="db_host" value="<?php echo $db_host; ?>">
                                        <input type="hidden" name="db_user" id="db_user" value="<?php echo $db_user; ?>">
                                        <input type="hidden" name="db_pass" id="db_pass" value="<?php echo $db_pass; ?>">
                                        <input type="hidden" name="db_name" id="db_name" value="<?php echo $db_name; ?>">
                                        <div style='text-align: right;'>
                                            <button type="submit" class="button is-link">Next</button>
                                        </div>
                                    </form><?php
                                        } else { ?>
                                    <form action="index.php?step=1" method="POST">
                                        <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                                        <input type="hidden" name="license_code" id="license_code" value="<?php echo $license_code; ?>">
                                        <input type="hidden" name="client_name" id="client_name" value="<?php echo $client_name; ?>">
                                        <div class="field">
                                            <label class="label">Database Host</label>
                                            <div class="control">
                                                <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Database Username</label>
                                            <div class="control">
                                                <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Database Password</label>
                                            <div class="control">
                                                <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Database Name</label>
                                            <div class="control">
                                                <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                                            </div>
                                        </div>
                                        <div style='text-align: right;'>
                                            <button type="submit" class="button db_import is-link">Import</button>
                                        </div>
                                    </form><?php
                                        }
                                    } else { ?>
                                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
                                                                                                    }
                                                                                                    break;
                                                                                                case "2": ?>
                            <div class="tabs is-fullwidth">
                                <ul>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Requirements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Verify</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span><i class="fa fa-check-circle"></i> Database</span>
                                        </a>
                                    </li>
                                    <li class="is-active">
                                        <a>
                                            <span><b>Finish</b></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                                                                                                    if ($_POST && isset($_POST["dbscs"])) {
                                                                                                        $valid = $_POST["dbscs"];
                                                                                                        $license_code = $_POST['license_code'];
                                                                                                        $client_name = $_POST['client_name'];
                                                                                                        $db_host = strip_tags(trim($_POST["db_host"]));
                                                                                                        $db_user = strip_tags(trim($_POST["db_user"]));
                                                                                                        $db_pass = strip_tags(trim($_POST["db_pass"]));
                                                                                                        $db_name = strip_tags(trim($_POST["db_name"]));
                            ?>
                                <center>
                                    <p><strong>Admin credential</strong></p><br>
                                </center>

                                <form action="#" id="admin-detal-form" method="POST">
                                    <div class="field">
                                        <label class="label">Admin Email</label>
                                        <div class="control">
                                            <input class="input" type="email" value="admin-mealUp@gmail.com" id="email" placeholder="Enter your admin email" name="email" required readonly>
                                            <p class="email" style='color:#f74416;'></p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="db_host" value="<?php echo $db_host; ?>">
                                    <input type="hidden" name="db_user" value="<?php echo $db_user; ?>">
                                    <input type="hidden" name="db_pass" value="<?php echo $db_pass; ?>">
                                    <input type="hidden" name="db_name" value="<?php echo $db_name; ?>">
                                    <input type="hidden" name="license_code" value="<?php echo $license_code; ?>">
                                    <input type="hidden" name="client_name" value="<?php echo $client_name; ?>">

                                    <div class="field">
                                        <label class="label">Admin Password</label>
                                        <div class="control">
                                            <input class="input" type="text" id="password" placeholder="Enter your admin password" value="123456" name="password" readonly required>
                                            <p class="password" style='color:#f74416;'></p>
                                        </div>
                                    </div>
                                    <div style='text-align: right;'>
                                        <button type="button" id="admin-login-btn" class="button is-link">Procced to Login</button>
                                    </div>
                                </form>
                            <?php
                                                                                                    } else { ?>
                                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
                                                                                                    }
                                                                                                    break;
                                                                                            } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="content has-text-centered">
        <p>Copyright <?php echo date('Y'); ?> Company, All rights reserved.</p><br>
    </div>
</body>

<script>
    jQuery(document).ready(function($) {
        $(".db_import").on('click', function(event) {
            $('#cover-spin').show(0)
        });
    });

    jQuery(document).on("click", "#admin-login-btn", function()
    {
        $('#cover-spin').show(0);
        url = window.location.origin + window.location.pathname
        url = url.slice(0, -1)
        var name = url.substring(0, url.lastIndexOf("license"));
        var formData = new FormData($('#admin-detal-form')[0]);
        // console.log(formData);
        $.ajax({
            type: "POST",
            url: name + "public/saveEnvData",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result)
            {
                // if (result.success == true)
                // {
                //     window.location.replace(result.data);
                // }
                $.ajax(
                {
                    type: "POST",
                    url: name + "public/saveAdminData",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result)
                    {
                        console.log('result', result)
                        if (result.success == true)
                        {
                            window.location.replace(result.data);
                        }
                    },
                    error: function(err) {

                    }
                });
            },
            error: function(err) {

            }
        });
    });
</script>

</html>
