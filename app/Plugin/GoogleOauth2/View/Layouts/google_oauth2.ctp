<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="<?php echo SITE_BASE . "js/skel.min.js"; ?>">
        {
          prefix: "<?php echo SITE_BASE; ?>css/style",
          breakpoints: {
            wide: { range: "1200-", containers: 1140, grid: { gutters: 50 } },
            narrow: { range: "481-1199", containers: 960 },
            mobile: { range: "-480", containers: "fluid", lockViewport: true, grid: { collapse: true } }
          }
        }            
        </script>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet" />
    </head>
    <body>
        <?php echo $this->Session->flash(); ?>

        <?php echo $this->fetch('content'); ?>
    </body>
</html>
