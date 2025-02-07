<?php

include '../backend/dbConfig.php';

function redirectToSignIn() {
    session_destroy();
    header("Location: ../signin");
    exit();
}

if (!isset($_SESSION['token'])) {
    redirectToSignIn();
}

$loggedInToken = $_SESSION['token'];
$stmt = $con->prepare("SELECT * FROM users WHERE user_token = ?");
$stmt->bind_param("s", $loggedInToken);
$stmt->execute();
$verify_token = $stmt->get_result();
$stmt->close();

if ($verify_token->num_rows === 0) {
    redirectToSignIn();
}

$userdata = $verify_token->fetch_assoc();
$stmt->close();

$server = mysqli_query($con, "SELECT * FROM server");
$count_server = mysqli_num_rows($server);

if ($count_server == 0) {
    redirectToSignIn();
}

$fetch_server = mysqli_fetch_assoc($server);

if ($userdata['user_perms'] != "owner" && in_array($fetch_server['server_status'], ["maintenance", "offline"])) {
    redirectToSignIn();
}

if (in_array($userdata['status'], ["suspended", "not_verified"])) {
    redirectToSignIn();
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $checkToken = mysqli_query($con, "SELECT * FROM auth WHERE token = '" . mysqli_real_escape_string($con, $token) . "'");

    if (mysqli_num_rows($checkToken) > 0) {
        $fetchToken = mysqli_fetch_assoc($checkToken);
        
        if ($fetchToken['panel_user'] !== NULL) {
            header("Location: https://t.me/$bot");
            exit();
        }

    } else {
        header("Location: https://t.me/$bot");
        exit();
    }

    if (isset($_POST['final'])) {
        $updateUser_tg = mysqli_query($con, "UPDATE users SET tg_user_id = '" . mysqli_real_escape_string($con, $fetchToken['user_id']) . "', tg_chat_id = '" . mysqli_real_escape_string($con, $fetchToken['chat_id']) . "', tg_username = '" . mysqli_real_escape_string($con, $fetchToken['user']) . "' WHERE unique_code = '" . mysqli_real_escape_string($con, $userdata['unique_code']) . "'");

        if (!$updateUser_tg) {
            $response = array("statusCode" => 407);
            echo json_encode($response);
            exit();
        }

        $updateAuthToken = mysqli_query($con, "UPDATE auth SET panel_user = '" . mysqli_real_escape_string($con, $userdata['username']) . "' WHERE token = '" . mysqli_real_escape_string($con, $token) . "'");

        if (!$updateAuthToken) {
            $response = array("statusCode" => 407);
            echo json_encode($response);
            exit();
        }

        header("Location: https://t.me/$bot");
        exit();
    }
} else {
    header("Location: https://t.me/$bot");
    exit();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Auth | <?php echo $title ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $logo ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit&family=Rajdhani:wght@600&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
   
    <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css"
      >

      <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-thin.css"
      >

      <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-solid.css"
      >

      <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-regular.css"
      >

      <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-light.css"
      >

    <link rel="stylesheet" href="../assets/vendor/libs/spinkit/spinkit.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/cards-advance.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../assets/vendor/js/customizer.js"></script>
    <script src="../assets/js/config.js"></script>
  </head>

  <body style="font-family: Outfit, sans-serif;">
   
    
<script>
$(document).ready(function() {
$('.btn-login').click(function(e){
	e.preventDefault();
	const divElement = document.querySelector('.modal-body-login');	
	var login = $('#login').val();
    if (login == '') {
    divElement.innerHTML = '<i class="fas fa-circle-exclamation text-danger" style="margin-left:auto; margin-right:auto; font-size: 38px; "></i><div style="font-size: 16px; padding-top: 10px"><span class="text-danger">Please Enter Your Username</span></div>';
    
    return false;
    }
    var password = $('#password').val();
    if (password == '') {
    divElement.innerHTML = '<i class="fas fa-circle-exclamation text-danger" style="margin-left:auto; margin-right:auto; font-size: 38px; "></i><div style="font-size: 16px; padding-top: 10px"><span class="text-danger">Please Enter Your Password</span></div>';

    return false;
    }	
	   $.ajax ({
         type: "POST",
	     url: "auth.g",
         data: "login="+login+"&password="+password,
	     beforeSend: function() {
         divElement.innerHTML = '<div class="spinner-border text-success float-centet" role="status" style="height: 38px; width: 38px; font-size: 7px"></div><div style="font-size: 16px; padding-top: 10px"><span class="text-success">Signing In</span></div>';
         
	     },	
	     success: function(response) {
         var auth = JSON.parse(response);
         if (auth.statusCode == 401) {
           divElement.innerHTML = auth.res;
         } else if (auth.statusCode == 403) {
           divElement.innerHTML = auth.res;
         } else if (auth.statusCode == 412) {
           function hideLoginModal() {
           $('#modal2').modal('hide');
           }   
           function openVerModal() {
           $('#validation_modal').modal('show');
           }    
           setTimeout(hideLoginModal, 500);
           setTimeout(openVerModal, 600);
         } else if (auth.statusCode == 200) {
           function redirect() {
           window.location.href = "profile";
           }
           
           divElement.innerHTML = auth.res;
           setTimeout(redirect, 1000);  
        }
		},
		error: function(err) {
		   var er = err;
		   divElement.innerHTML = '<i class="fas fa-wifi-exclamatiofa text-danger" style="margin-left:auto; margin-right:auto; font-size: 38px; "></i><div style="font-size: 16px; padding-top: 10px"><span class="text-danger">No Internet Connection</span></div>';
		   $('#prb-container').html('<div class="progress-bar-value-warning">');
		}            
	});
  });     
});
function validateCode() {
var code = $('#verification_code').val();
    if (code == '') {
    $('#code_empty').show();
    return false;
    }
    $.ajax ({
        type: "POST",
        url: "web.g",
        data: "action=verify&code="+code,
	    beforeSend: function() {
	    $('#code_empty').hide();
	    $('#code_incorrect').hide();
        },	
	    success: function(response) {
        var auth = JSON.parse(response);
        if (auth.statusCode == 200) {
        function hideValidationModal() {
           $('#validation_modal').modal('hide');
        }   
        const modal_body = document.querySelector('.modal-body-login');
        modal_body.innerHTML = auth.res2;
         
        function redirect() {
          window.location.href = "profile";
        }    
          function openLoginModal() {
          $('#modal2').modal('show');
          }  
        setTimeout(openLoginModal, 600);
        setTimeout(redirect, 1500);    
        setTimeout(hideValidationModal, 500);
        
        } else if (auth.statusCode == 401) {
        $('#code_incorrect').show();
        }
        }
        
    });
    
}

</script>
<style>
.progress-bar {
  height: 2px;
  background-color: transparent;
  border-top-left-radius: 50px;
  border-top-right-radius: 50px;
  width: 100%;
  border: 0.0px solid #161618;
  overflow: hidden;
}
.progress-bar-value-success {
  height: 100%;
  width: 100%;
  background: linear-gradient(to right, #40826D, #98FB98);
}
.progress-bar-value-warning {
  height: 100%;
  width: 100%;
  background: linear-gradient(to right, #ff9f43, #FFDF00);
}
.progress-bar-value-danger {
  height: 100%;
  width: 100%;
  background: linear-gradient(to right, #D21F3C, #ea5455);
}
.progress-bar-value {
  height: 100%;
  width: 100%;
  background: linear-gradient(to right, #40826D, #98FB98);
  animation: indeterminateAnimation 1s infinite linear;
  transform-origin: 0% 50%;
}

@keyframes indeterminateAnimation {
  0% {
    transform:  translateX(0) scaleX(0);
  }
  40% {
    transform:  translateX(0) scaleX(0.4);
  }
  100% {
    transform:  translateX(100%) scaleX(0.5);
  }
}
</style>
      
       
         <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
                    <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center justify-content-center bg-navbar-theme text-center"
            id="layout-navbar"
          >
<div class="text-center" style="height: auto; padding: 13px; border-top-left-radius: 5px; border-top-right-radius: 5px; font-size: 20px">   
       <img src="../geekmods.png" style="margin-bottom: 4.5px;" height="21" width="21">                      
<b><font class="text-success"><?php echo $web1 ?><font class="text-muted"><?php echo $web2 ?></font></font></b>
           </div>    
</nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <!-- Content Removed for Brevity -->
                  <div class="col-lg-3 col-sm-6 mb-3 text-center" style="">
                
                
                
                
                
                
                


          
                                              <div class="card mb-4" style="background: <?php if ($userdata['user_banner'] == 'static.png' || $userdata['user_banner'] == NULL) { ?> 
                    <?php if ($userdata['user_color'] == 'success') { ?>
            linear-gradient(to right, #40826D, #28c76f); 
            <?php } else if ($userdata['user_color'] == 'warning') { ?>
            linear-gradient(to right, #ff9f43, #FFDF00);
            <?php } else if ($userdata['user_color'] == 'danger') { ?>
            linear-gradient(to right, #D21F3C, #ea5455);
            <?php } ?>
            <?php } else { ?> url('../<?php echo $userdata['user_banner']; ?>'); <?php } ?> background-size: cover; background-repeat: no-repeat; background-position: center; border-radius: 4px: padding-top: 0%; height: auto; ">   

                                                                                                                      <div class="avatar avatar-lg mb-2 mt-2 pb-3" style="margin-left: auto; margin-right: auto;">
                                                                                               <?php if ($userdata['user_avatar'] == 'geekmods.png' || $userdata['user_avatar'] == NULL) { ?>
                                                                      <span class="avatar-initial rounded-circle bg-label-<?php echo $userdata['user_color']; ?>"><?php $first_letter = substr($userdata['username'], 0, 1); echo $first_letter; ?></span>
                         <?php } else { ?>
                              <img src="../<?php echo $userdata['user_avatar']; ?>" alt class="h-auto rounded-circle" />
                              <?php } ?>
                            </div>
                        
               </div>             
                <div style="display: flex; align-items: center; justify-content: center;">       <div class="p-2" style="width: auto; display: inline-block; border-radius: 4px">  <img src="../geekmods.png" width="30" style=""><br> @<?php echo $userdata['username']; ?> </div>
                         
                         <i class="fa-duotone fa-link" style="margin-left: 15px; margin-right: 15px; font-size: 20px"></i> 
                         
                         <div class="p-2" style="width: auto; display: inline-block; border-radius: 4px">  <img src="../telegram.png" width="30"> <br>@<?php echo $fetchToken['user']; ?></div></div>
                                        
                   <form action="" method="post">         <button value="true" name="final" type="submit" class="btn btn bg-success mt-4 p-2 btn-lg text-white w-100" style="border-radius: 4px;">Continue as <b> <span class="mx-1"><?php echo $userdata['username']; ?></span></b></button>  </form>   



                


            
        
            
            
             
            <!-- / Content -->


            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->


           
           
           
 <!-- Modal Start -->
 <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true" data-backdrop="static">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content p-0" style="box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); ">
 <div class="modal-header p-0" style="border-top-left-radius: 3px; border-top-right-radius: 3px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">

  </div>  
 <div id="modalBody" class="modal-body modal-body-login p-4 text-center" style="border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-left-radius: 3px; border-bottom-right-radius: 3px; border-top: 0px solid #ea5455; ">
 
 </div>
 
 </div>
 </div>
 </div>
 </div>
<!-- Modal End-->



 <!-- Validation Modal Start -->
 <div class="modal fade" id="validation_modal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true" data-backdrop="static">
 <div class="modal-dialog modal-dialog-centered">
<div class="modal-content p-0" style="box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); ">
 <div class="modal-header p-0" style="border-top-left-radius: 3px; border-top-right-radius: 3px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">

  </div>  
 <div id="modalBody" class="modal-body p-4" style="">
 
 
 <div class="bg-label-success p-3 text-center" style="margin-top: 0px; border-radius: 6px; font-size: 14px"><i class="fa-duotone fa-envelope-dot" style="font-size: 34px; margin-bottom: 8px"></i><br>
We Have Sent An Verification Code To Your Your Email Address, Enter That Code Below To Proceed
</div>

  <div class="mt-3" style="margin-bottom: 18.5px;">                                      <div class="form-floating mt-2 mb-3" style="">
          <input type="number" class="form-control" id="verification_code" placeholder="" value="" aria-describedby="floatingInputHelp" />
          <label for="verification_code">VERIFICATION CODE</label>
                                            <div id="code_incorrect" class="text-danger" style="margin-top: 5px; display: none"><i class="fad fa-circle-exclamation"></i> Incorrect Verification Code </div>
</div>

                                    </div>
                                    
   <div class="d-flex justify-content-end">
  <button type="button" onclick="validateCode();" class="btn btn-success w-50" style="">Proceed</button>
</div>
                 
                                    

 
 </div>
 </div>
 </div>
 </div>
 </div>
<!-- Modal End-->


           
           
           
           
           

          </tbody>
        </table>
      </div>
    

            </div>
          
        </div>
      
      
    <!-- Core JS -->
    <!-- build:js ../assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../assets/vendor/libs/swiper/swiper.js"></script>
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <script>
      function tutorial(link) {
        document.location.href = link;
      }
      function demo(link) {
        document.location.href = link;
      }
      function download(link) {
        document.location.href = link;
      }
    </script>
    <!-- Page JS -->
    <script src="../assets/js/cards-actions.js"></script>

    <script src="../assets/js/dashboards-analytics.js"></script>
  </body>
</html>
