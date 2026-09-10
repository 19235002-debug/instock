<!-- Form Filter Status di tengah -->
<div>
                    <form method="GET" action="" class="d-inline-block mr-3">
                        <select name="filter_status" class="form-control" onchange="this.form.submit()">
                            <option value="all">Semua</option>
                            <option value="active" <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'active') echo 'selected'; ?>>Active</option>
                            <option value="not active" <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'not active') echo 'selected'; ?>>Not Active</option>
                        </select>
                    </form>
                </div>