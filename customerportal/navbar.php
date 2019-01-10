<?php
$menu = array(

    array(
        "name" => "Reservation",
        "link" => "#",
    ),
    array(
        "name" => "Booking",
        "link" => "#",
    ),
    array(
        "name" => "Track N Trace",
        "link" => "#",
    ),
    array(
        "name" => "Ticket",
        "link" => "#",
    ),
);
?>
<nav class="navbar navbar-expand-lg bg-danger">
    <div class="container">
      <div class="navbar-translate">
        <a class="navbar-brand" href="#pablo">GP EXPRESS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#example-navbar-danger"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-bar bar1"></span>
          <span class="navbar-toggler-bar bar2"></span>
          <span class="navbar-toggler-bar bar3"></span>
        </button>
      </div>
      <div class="collapse navbar-collapse" id="example-navbar-danger">
        <ul class="navbar-nav ml-auto">
            <?php foreach ($menu as $key => $value): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $value['link']; ?>">
              <p><?php echo $value['name']; ?></p>
            </a>
          </li>
          <?php endforeach;?>
        </ul>
      </div>
    </div>
  </nav>
