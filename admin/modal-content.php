<div class="modal-dialog my-plugin-modal-dialog" role="document">
    <div class="modal-content my-plugin-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Scraped Data</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <?php 
                $response = json_decode(stripslashes($_GET['response']), true);
                echo "<p><strong>Title:</strong> " . $response['title'] . "</p>";
                echo "<p><strong>Description:</strong> " . $response['description'] . "</p>";
                echo "<p><strong>Short Description:</strong> " . $response['short_description'] . "</p>";
                echo "<p><strong>Price:</strong> " . $response['price'] . "</p>";
            ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
