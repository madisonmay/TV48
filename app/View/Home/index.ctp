<?php if ($this->Session->read('Auth.User') && in_array('landlord', $this->Session->read('User.roles'))): ?>

    <div class="row-fluid">
        <div class="span3">
            <a href='/sensors/lighting'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='/sensors/heating'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='/sensors/electricity'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        
        <div class="span3">
            <a href='/home/manage'><div class="border">
                <div id="management" class="circle">
                    <div class="opaque">
                        <div class="label-text">Manage</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!$this->Session->read('Auth.User') || !in_array('landlord', $this->Session->read('User.roles'))): ?>
    <div class="row-fluid">
        <div class="span4">
            <a href='light'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span4">
            <a href='heat'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span4">
            <a href='power'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- <img src='images/left.png' style='width: 5%; float: left;'></img> -->
<div id='chart' style='height: 350px; width: 85%; margin-right: auto; margin-left: auto;'>
    <svg></svg>
<div>
<!-- <img src='images/right.png' style='width: 5%; float: right;'></img> -->
</div>