<div id="header"><!-- start header -->
    <div id="showcase" class="row spaced">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php
                if (count($lastRequests) > 0) {
                    foreach ($lastRequests as $req) {
                        echo '<div class="my-wrap">'
                            . '<img class="show-user-info" onload="$(this).fadeIn(1200);" rel="tooltip" '
                            .'data-user="'.$req->username.'" data-toggle="tooltip" data-placement="top" '
                            .'src="'.url('avatar/64/'.$req->uuid).'" alt="'.$req->username.'" '
                            .'title="'.$req->username.'" />'
                            . '</div>'."\n";
                    }
                } else {
                    for ($i=0;$i<9;$i++) {
                        echo '<div class="my-wrap">';
                        echo '<img onload="$(this).fadeIn(1200);" rel="tooltip" data-toggle="tooltip" '.
                        'data-placement="top" src="'.url("avatar/64/_Cyb3r").'" alt="_Cyb3r" title="_Cyb3r"/>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div><!-- end header -->

<div class="row">
    <div class="col-md-12">
        <div id="search-form" class="row"><!-- start search-form -->
            <div class="col-md-6 col-md-offset-3">
                <div class="input-group">
                    <input id="user-search" class="form-control" placeholder="Minecraft username..." autocomplete="off" type="text" name="username">
                    <span class="input-group-btn">
<button id="user-search-butt" class="btn btn-info">Find!</button>
</span>
                </div>
            </div>
        </div><!-- end search-form -->
        <div id="search-alert" class="row"><div class="col-md-6 col-md-offset-3"></div></div>
    </div>
</div>
</div>
<div class="main-cont container core">
    <div id="box">
        <div class="row">
            <div class="col-md-6">
                <div class="block">
                    <h3>Usage</h3>
                    This is the simply way to get an avatar
<pre>
&lt;img src="<?php echo url(); ?>/avatar/<span class="pre-api-param">{uuid|username}</span>" />
</pre>
                    Bigger is better
<pre>
&lt;img src="<?php echo url(); ?>/avatar/<span class="pre-api-param">512</span>/<span class="pre-api-param">{uuid|username}</span>" />
</pre>

                    <b style="color: red;">NEW!</b> Isometric head
<pre>
&lt;img src="<?php echo url(); ?>/head/<span class="pre-api-param">{uuid|username}</span>" />
&lt;img src="<?php echo url(); ?>/head/<span class="pre-api-param">256</span>/<span class="pre-api-param">{uuid|username}</span>" />
</pre>
                    If you need 2D skin
<pre>
&lt;img src="<?php echo url(); ?>/skin/<span class="pre-api-param">{uuid|username}</span>" />
&lt;img src="<?php echo url(); ?>/skin/<span class="pre-api-param">{size}</span>/<span class="pre-api-param">{uuid|username}</span>" />
</pre>
                    2D back skin
<pre>
&lt;img src="<?php echo url(); ?>/skin-back/<span class="pre-api-param">{uuid|username}</span>" />
</pre>
                    Download the full skin image
<pre>
<?php echo url(); ?>/download/<span class="pre-api-param">{uuid}</span>
</pre>
                    Can I force update of my avatar/skin on MinePic? Sure!
<pre>
<?php echo url(); ?>/update/<span class="pre-api-param">{uuid}</span>
</pre>
                </div>
            </div>
            <div class="col-md-3">
                <div class="block">
                    <h3>Most Wanted</h3>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>Username</td>
                            <td>Requests</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($mostWanted) > 0) {
                            foreach ($mostWanted as $mw) {
                                echo '<tr>';
                                echo '<td><a class="no-decoration" href="'.url('user/'.$mw->username).'">';
                                echo '<img src="'.url('avatar/16/'.$mw->uuid).'" alt="'.$mw->username.'" title="'.$mw->username.'"></a> <a href="'.url('user/'.$mw->username).'">'.$mw->username.'</td>';
                                echo '<td style="text-align: right;">'.number_format($mw->count_request, 0, '.', '.').'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="block">

                    <div class="block" style="word-break: break-all;">

                        <h3>Donors list</h3>
                        <a target="_blank" href="<?php echo url('user/hackLover'); ?>">hackLover</a><br />
                        <a target="_blank" href="<?php echo url('user/BaluMonster'); ?>">BaluMonster</a><br />
                        <a target="_blank" href="<?php echo url('user/cd42f23f2edd4e7982a176464a9603a8'); ?>">zKerbs</a><br />
                        <a target="_blank" href="<?php echo url('user/Giulio17'); ?>">Giulio17</a><br />
                        <a target="_blank" href="<?php echo url('user/terminetor1717'); ?>">terminetor1717</a><br />
                        <a target="_blank" href="https://github.com/lucapitzoi">Luca Pitzoi</a><br /><br />

                        <table style="width: 100%;">
                            <tr>
                                <td style="background: none;">
                                    <form style="margin-bottom: 5px;margin-left: -10px;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_donations"><input type="hidden" name="business" value="mattia.basone@gmail.com"><input type="hidden" name="lc" value="IT"><input type="hidden" name="item_name" value="minepic.org"><input type="hidden" name="no_note" value="0"><input type="hidden" name="currency_code" value="EUR"><input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>with PayPal
                                </td>
                            </tr>
                        </table>
                        <br /><br />

                        <h3>Who uses MinePic</h3>
                        <a target="_blank" href="https://www.minecraft-italia.it">Minecraft ITALIA</a><br />
                        <br />Are you using MinePic? <a href="mailto:info@minepic.org">Tell us!</a>
                    </div>
                    <div class="block" style="margin-top: 20px;">
                        <h3>Tools</h3>
                        <a href="http://wordpress.org/plugins/minepic/">WordPress Plugin</a>
                        (made by <a href="http://profiles.wordpress.org/raynlegends/">RaynLegends</a>)<br />
                    </div>
                </div>
            </div>
        </div>