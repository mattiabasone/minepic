<div class="row"><!-- first row -->
    <div class="col-md-12"><!-- col-md-12 -->
        <div class="row">
            <div class="col-md-12">
                <h3 id="user-title"><?php echo $user['username']; ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <table class="table table-bordered table-responsive table-userinfo">
                    <tr>
                        <td>Minecraft ID</td>
                        <td><?php echo $user['uuid']; ?></td>
                    </tr>
                    <tr>
                        <td>Total requests</td>
                        <td><?php echo $user['count_request']; ?></td>
                    </tr>
                    <tr>
                        <td>Last request</td>
                        <td><?php echo $user['last_request']; ?></td>
                    </tr>
                    <tr>
                        <td>Total search</td>
                        <td><?php echo $user['count_search']; ?></td>
                    </tr>
                    <tr>
                        <td>Last search</td>
                        <td><?php echo $user['last_search']; ?></td>
                    </tr>
                </table>
                <hr />
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn btn-minepic btn-lg" href="<?php echo url('download/'.$user['uuid']); ?>">Download Skin</a>
                    </div>
                    <div class="col-md-6">
                        <a class="btn btn-minepic btn-lg" href="http://minecraft.net/profile/skin/remote?url=<?php echo url('download/'.$user['uuid'].'.png'); ?>">Use this Skin</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?php echo url('avatar/'.$user['uuid']); ?>" alt="<?php echo $user['username']; ?>" title="<?php echo $user['username']; ?>" />
                    </div>
                    <div class="col-md-4">
                        <img src="<?php echo url('skin/'.$user['uuid']); ?>" alt="<?php echo $user['username']; ?>" title="<?php echo $user['username']; ?>" />
                    </div>
                    <div class="col-md-4">
                        <img src="<?php echo url('skin-back/'.$user['uuid']); ?>" alt="<?php echo $user['username']; ?>" title="<?php echo $user['username']; ?>" />
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="<?php echo url('avatar/'.$user['uuid']); ?>" readonly />
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="<?php echo url('skin/'.$user['uuid']); ?>" readonly />
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="<?php echo url('skin-back/'.$user['uuid']); ?>" readonly />
                    </div>
                </div>
            </div>
        </div>
        <hr />
    </div><!-- close col-md-12 -->
</div><!-- close first row -->