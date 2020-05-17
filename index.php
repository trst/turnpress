<?php
  /**
   * Redirect to actual website
   */

  $redirect_url = get_option("redirect_url");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redirecting...</title>
  <meta http-equiv="refresh" content="0;<?php echo $redirect_url; ?>" />
</head>
<body>
  Redirecting... 
</body>
</html>
