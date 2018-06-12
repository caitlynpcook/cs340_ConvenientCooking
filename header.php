<header>
    <div>
        <div>
            Logo!
        </div>
        <nav>
            <ul>
            <?php
            foreach ($content as $page => $location){
                echo "<li><a href='$location'".($page==$currentpage?" class='active'":"").">".$page."</a></li>";			
            }
            ?>
            </ul>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if($_POST['action'] == "logout"){
                        echo "Logging you out!";
                        session_destroy();
                  }
                }
            ?>
            <form method="post" id="login">
                <input type="hidden" name="action" value="logout">
                <input type="submit"  value=" Logout " />
            </form>
        </nav>
    </div>
</header>
