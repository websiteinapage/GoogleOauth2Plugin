<?php $this->layout = "google_oauth2"; ?>
<div class="row">
    
    <article class="3u">
        &nbsp;
    </article>
    
    <article class="6u">
        <?php if(!empty($user)): 
            // print_r($user);
            echo "<h1>Welcome {$user['given_name']} &lt;{$user['email']}&gt;!</h1>";
            ?>
            <div class="button-bar">
                <a href="<?php echo SITE_BASE . "google_oauth2/auth/logout"; ?>" class="gbutton">Logout</a>
            </div>
            
        <?php else: ?>
            <h1>Login</h1>
            This is a customizable login screen.
            <div class="button-bar">
                <a href="<?php echo SITE_BASE . "google_oauth2/auth/connect"; ?>" class="gbutton">Login with <i class="fa fa-google-plus"></i></a>
            </div>
        <?php endif; ?>
    </article>
    
    <article class="3u">
        &nbsp;
    </article>
</div>
