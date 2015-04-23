<div class="errorwrapper error404">
	<div class="errorcontent">
        <h1>404 Page Not Found</h1>
        <h3>We couldn't find that page. It appears that you are lost.</h3>
        
        <p>The page you are looking for is not found. This could be for several reasons</p>
        <ul>
            <li>It never existed.</li>
            <li>It got deleted for some reason.</li>
            <li>You were looking for something that is not here.</li>
        </ul>
        <br />
        <button class="stdbtn btn_black" onclick="history.back()">Go Back to Previous Page</button> &nbsp; 
        <button onclick="location.href='<?=url('dashboard/back/index');?>'" class="stdbtn btn_orange">Go Back to Dashboard</button>
    </div><!--errorcontent-->
</div><!--errorwrapper-->