<?php
class Categories extends General {



    function main_categories($order)
    {
        $sql = "SELECT * FROM ss_categories ORDER BY $order";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

    }

    function categories_query($where,$order)
    {
        $sql = "SELECT * FROM ss_categories $where ORDER BY $order";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

    }


    function add_category($request)
    {
        $category = make_safe(xss_clean($request['category']));
        $seo_keywords = make_safe(xss_clean($request['seo_keywords']));
        $seo_description = make_safe(xss_clean($request['seo_description']));
        if (empty($category)) {
            $message = notification('warning','Insert Category Please.');
        } else {
            $sql = "INSERT INTO ss_categories (category,seo_keywords,seo_description) VALUES ('$category','$seo_keywords','$seo_description')";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Category Added Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function edit_category($request,$id)
    {
        $category = make_safe(xss_clean($request['category']));
        $seo_keywords = make_safe(xss_clean($request['seo_keywords']));
        $seo_description = make_safe(xss_clean($request['seo_description']));
        if (empty($category)) {
            $message = notification('warning','Insert Category Please.');
        } else {
            $sql = "UPDATE ss_categories SET category='$category',seo_keywords='$seo_keywords',seo_description='$seo_description' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Category Edited Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function category($id)
    {
        $sql = "SELECT * FROM ss_categories WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function move_category($request,$id)
    {
        $new_category = make_safe(xss_clean($request['category_id']));
        if (!isset($new_category)) {
            $message = notification('warning','Please Select a Category that you want to move the Sources to.');
        } else {
            $this->db->query("UPDATE ss_services SET category_id='$new_category' WHERE category_id='$id'");
            $delete = $this->db->query("DELETE FROM ss_categories WHERE id='$id'");
            if ($delete) {
                $message = notification('success','Services Moved and Category Deleted Successfully.');
                $done = true;
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        $result = array('message' => $message, 'done' => $done);
        return $result;
    }

    function delete_category($id)
    {
        $delete = $this->db->query("DELETE FROM ss_categories WHERE id='$id'");
        if ($delete) {
            $message = notification('success','Category and All related Sources and News Deleted Successfully.');
            $done = true;
        } else {
            $message = notification('danger','Error Happened.');
        }
        $result = array('message' => $message, 'done' => $done);
        return $result;
    }

    function get_category_services($id)
    {
        $sql = "SELECT * FROM ss_services WHERE category_id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            return $query->num_rows;
        }
    }
}