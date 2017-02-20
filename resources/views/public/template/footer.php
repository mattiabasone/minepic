<div id="footer"><!-- start footer -->
    <div class="row">
        <div class="col-md-4">
            <div class="block">
                <h3>GitHub</h3>
                Source code<br />
                <a target="_blank" href="https://github.com/mattiabasone/minepic-api">https://github.com/mattiabasone/minepic-api</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="block">
                <h3>Friends</h3>
                <a href="http://www.minepick.com/hungergames/">Minepick</a>, check its Hunger Games Server List!
            </div>
        </div>
        <div class="col-md-4">
            <div class="block">
                <h3>Powered by</h3>
                <a href="https://php.net/" target="_blank">PHP</a>,
                <a href="https://www.freebsd.org/" target="_blank">FreeBSD</a> ,
                <a href="https://nginx.org" target="_blank">nginx</a> and
                <a href="https://mariadb.org/" target="_blank">MariaDB</a>
            </div>
        </div>
    </div>
</div><!-- end footer -->

<div id="copy"><!-- start copy -->
    <div class="row">
        <div class="col-md-12">
            MinePic &copy; 2013-<?php echo date("Y"); ?> - All rights reserved<br />
            SSL by <a href="https://letsencrypt.org/">Let’s Encrypt</a> ❤️
        </div>
    </div>
</div><!-- close copy -->
</div>
</div>
<!-- Modal di hack-->
<div id="user-info-modal" class="modal fade user-info-modal" tabindex="-1" role="dialog" aria-labelledby="user-info-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modal-username-title" class="modal-title">Steve</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form>
                            <div class="row">
                                <div class="col-md-4">
                                    <img id="modal-img-avatar" src="<?php echo url('avatar/Steve'); ?>" alt="Steve" title="Steve" />
                                </div>
                                <div class="col-md-4">
                                    <img id="modal-img-skin" src="<?php echo url('skin/Steve'); ?>" alt="Steve" title="Steve" />
                                </div>
                                <div class="col-md-4">
                                    <img id="modal-img-skin-back" src="<?php echo url('skin-back/Steve'); ?>" alt="Steve" title="Steve" />
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-4">
                                    <input id="modal-input-avatar" class="form-control" type="text" value="<?php echo url('avatar/Steve'); ?>" readonly />
                                </div>
                                <div class="col-md-4">
                                    <input id="modal-input-skin" class="form-control" type="text" value="<?php echo url('skin/Steve'); ?>" readonly />
                                </div>
                                <div class="col-md-4">
                                    <input id="modal-input-skin-back" class="form-control" type="text" value="<?php echo url('skin-back/Steve'); ?>" readonly />
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-4">
                                    <a id="modal-btn-download" class="btn btn-minepic btn-lg" href="<?php echo url('download/Steve'); ?>">Download Skin</a>
                                </div>
                                <div class="col-md-4">
                                    <a id="modal-btn-change" class="btn btn-minepic btn-lg" href="http://www.minecraft.net/skin/remote.jsp?url=<?php echo url('download/Steve.png'); ?>">Use this Skin</a>
                                </div>
                                <div class="col-md-4">
                                    <a id="modal-btn-user" class="btn btn-minepic btn-lg" href="<?php echo url('user/Steve'); ?>">View MinePic profile</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br />
<!-- Fine modal -->
<script type="text/javascript" src="<?php echo url('/assets/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('/assets/js/typeahead.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('/assets/js/functions.js'); ?>"></script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-39562562-1', 'auto');
    ga('require', 'displayfeatures');
    ga('send', 'pageview');
</script>
</body>
</html>