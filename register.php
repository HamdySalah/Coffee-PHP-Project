<?php require "includes/header.php"; ?>
<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 ftco-animate">
                <form action="#" class="billing-form ftco-bg-dark p-4 p-md-5">
                    <h3 class="mb-4 billing-heading">Add User</h3>
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Username">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="text" class="form-control" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Password">Password</label>
                                <input type="password" class="form-control" name="pass" placeholder="Password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="CPassword">Confirm Password</label>
                                <input type="password" class="form-control" name="cpass" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="room">Room No.</label>
                                <input type="text" class="form-control" name="room" placeholder="Room No.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ext">EXT</label>
                                <input type="text" class="form-control" name="ext" placeholder="EXT">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pic">Profile Picture</label>
                                <input type="file" name="pic" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mt-4">
                                <button type="submit" name="submit" class="btn btn-primary py-3 px-4">Save</button>
                                <button type="reset" name="reset" class="btn btn-secondary py-3 px-4">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require "includes/footer.php"; ?>