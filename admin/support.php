<?php
include('header.php');
?>
<div class="big-title">
    <h1>Support</h1>
    <div class="actions">
        <a href="<?php echo help_links('support'); ?>" target="_blank"><span>Help</span><i class="ri-question-line"></i></a>
    </div>
</div>
<?php
if (!empty($_GET['case'])) {
    $case = make_safe($_GET['case']);
} else {
    $case = '';
}
switch ($case) {
    case 'details';
    $id = make_safe(intval($_GET['id']));
    $support = $general->single_support_message($id);
?>
<div class="page-container">
    <div class="page-header">
        <h4><?php echo make_safe($support['title']); ?></h4>
        <div class="actions">
            <a href="support.php" class="btn btn-dark btn-sm float-right"><i class="ri-arrow-right-line"></i></a>
        </div>
    </div>
    <div class="form">
        <div class="message-details">
            <div class="message-meta">
                <ul>
                    <li><?php if($support['user_id'] > 0): ?><a href="customers.php?case=details&id=<?php echo make_safe($support['user_id']); ?>"><?php echo make_safe($support['username']); ?></a><?php else: ?><?php echo make_safe($support['username']); ?><?php endif; ?></li>
                    <li><?php echo make_safe($support['email']); ?></li>
                    <li><?php echo make_safe(date('j M, Y h:i a',$support['datetime'])); ?></li>
                </ul>
            </div>
            <div class="message-content">
                <p><?php echo make_safe(nl2br($support['message'])); ?></p>
            </div>
        </div>
        <div class="send-message">
            <div id="ajax-result-reply"></div>
            <form method="post" class="form" id="reply-form" action="">
                <div class="form-group">
                    <label for="reply">Reply <span>*</span></label>
                    <textarea id="reply" name="reply" class="form-control" rows="5" placeholder="Write your reply here"></textarea>
                </div>
                <input type="hidden" name="title" value="<?php echo make_safe($support['title']); ?>" />
                <input type="hidden" name="to_email" value="<?php echo make_safe($support['email']); ?>" />
                <input type="hidden" name="to_username" value="<?php echo make_safe($support['username']); ?>" />
                <input type="hidden" name="message_id" value="<?php echo make_safe($support['id']); ?>" />
                <button type="submit" id="reply-submit" class="btn btn-sm btn-secondary">Submit</button>
            </form>
        </div>
    </div>
</div>
<?php  break; default; ?>
<div class="page-container">
    <div class="page-header">
        <h4>All Support Messages</h4>
    </div>
    <div class="form">
    <?php
    if (isset($message)) {echo normalize_input($message);}
    $page = 1;
    $size = 20;
    if (isset($_GET['page'])){ $page = (int) $_GET['page']; }
    $sqls = "SELECT * FROM ss_support ORDER BY id DESC";
    $query = $mysqli->query($sqls);
    $total_records = $query->num_rows;
    if ($total_records == 0) {
        echo notification('warning','There Are No Support Messages right now.');
    } else {
        $pagination = new Pagination();
        $pagination->setLink("?page=%s");
        $pagination->setPage($page);
        $pagination->setSize($size);
        $pagination->setAlign('justify-content-end');
        $pagination->setTotalRecords($total_records);
        $get = "SELECT * FROM ss_support ORDER BY seen ASC, datetime DESC ".$pagination->getLimitSql();
        $q = $mysqli->query($get);
        ?>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th width="30"></th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $q->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="message-statue"><i class="icon <?php if($row['seen'] == 0): ?>icon-email<?php else: ?>icon-newsletter<?php endif; ?>"></i></td>
                        <td>
                            <div class="message-details">
                                <span class="badge badge-<?php if($row['source'] == 'contact form'): ?>secondary<?php else: ?>warning<?php endif; ?>"><?php echo make_safe($row['source']); ?></span><a href="support.php?case=details&id=<?php echo make_safe($row['id']); ?>"><?php echo make_safe($row['title']); ?></a>
                            </div>
                            <div class="customer-details">
                                <strong><?php if ($row['user_id'] > 0): ?>
                                    <a href="customers.php?case=details&id=<?php echo make_safe($row['user_id']); ?>"><?php echo make_safe($row['username']); ?></a>
                                <?php else: ?>
                                    <?php echo make_safe($row['username']); ?>
                                <?php endif; ?>
                                </strong>
                                On, <?php echo make_safe(date('d F, Y',$row['datetime'])); ?>
                                <?php if ($row['replied'] == 1): ?>
                                    <strong class="text-primary">Replied On, <?php echo make_safe(date('d F, Y',$row['reply_datetime'])); ?></strong>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <div class="news-actions">
                <?php echo normalize_input($pagination->create_links()); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php }
include('footer.php');
?>