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
  <?php if (!empty($redirect_url)) : ?>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;<?php echo $redirect_url; ?>" />
  <?php endif; ?>
</head>

<body>
  <?php if (!empty($redirect_url)) : ?>
    Redirecting... 
  <?php endif; ?>
</body>
</html>
