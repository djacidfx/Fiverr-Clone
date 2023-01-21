<?php
class Pages extends General {

    function add_page($request) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        $content = htmlentities(htmlspecialchars($request['content'],ENT_QUOTES));
        $seo_keywords = htmlspecialchars($request['seo_keywords'],ENT_QUOTES);
        $seo_description = htmlspecialchars($request['seo_description'],ENT_QUOTES);
        if (!empty($request['slug'])) {
            $slug = $this->make_page_slug(slugit(make_safe(xss_clean(htmlspecialchars($request['slug'],ENT_QUOTES)))),0);
        } else {
            $slug = $this->make_page_slug(slugit($title),0);
        }
        if (empty($title)) {
            $message = notification('warning','Insert The Title Please.');
        } elseif (empty($content)) {
            $message = notification('warning','Write Some content Please.');
        } else {
            $sql = "INSERT INTO ss_pages (title,content,slug,seo_keywords,seo_description) VALUES ('$title','$content','$slug','$seo_keywords','$seo_description')";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Page is Added Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function edit_page($request,$id) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        $content = htmlentities(htmlspecialchars($request['content'],ENT_QUOTES));
        $seo_keywords = htmlspecialchars($request['seo_keywords'],ENT_QUOTES);
        $seo_description = htmlspecialchars($request['seo_description'],ENT_QUOTES);
        if (!empty($request['slug'])) {
            $slug = $this->make_page_slug(slugit(make_safe(xss_clean(htmlspecialchars($request['slug'],ENT_QUOTES)))),$id);
        } else {
            $slug = $this->make_page_slug(slugit($title),$id);
        }
        if (empty($title)) {
            $message = notification('warning','Insert The Title Please.');
        } elseif (empty($content)) {
            $message = notification('warning','Write Some content Please.');
        } else {
            $sql = "UPDATE ss_pages SET title='$title',slug='$slug',content='$content',seo_keywords='$seo_keywords',seo_description='$seo_description' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Page Edited Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function all_pages($order)
    {
        $sql = "SELECT * FROM ss_pages ORDER BY $order";
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

    function page($id)
    {
        $sql = "SELECT * FROM ss_pages WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function make_page_slug($slug,$id)
    {
        if ($id == 0) {
            $sql = "SELECT * FROM ss_pages WHERE slug='$slug' LIMIT 1";
        } else {
            $sql = "SELECT * FROM ss_pages WHERE slug='$slug' AND id!='$id' LIMIT 1";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return $slug;
        } else {
            return $slug.'-'.$query->num_rows;
        }
    }
}