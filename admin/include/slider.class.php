<?php
class Slider extends General {

    function add_slide($request) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        $slide_title = make_safe(xss_clean(htmlspecialchars($request['slide_title'],ENT_QUOTES)));
        $slide_text = htmlspecialchars($request['slide_text'],ENT_QUOTES);
        if (!empty($_FILES['image']['name'])) {
            $up = new fileDir('../upload/slider/');
            $slide_image = $up->upload($_FILES['image']);
        } else {
            $slide_image = '';
        }
        if (empty($title)) {
            $message = notification('warning','Insert The Slide Title Please.');
        } elseif (empty($slide_image)) {
            $message = notification('warning','Please upload slide image.');
        } else {
            $sql = "INSERT INTO ss_slider (title,slide_title,slide_text,slide_image,slide_order) VALUES ('$title','$slide_title','$slide_text','$slide_image','0')";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Slide is Added Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }


    function edit_slide($request,$id) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        $slide_title = make_safe(xss_clean(htmlspecialchars($request['slide_title'],ENT_QUOTES)));
        $slide_text = htmlspecialchars($request['slide_text'],ENT_QUOTES);
        if (!empty($_FILES['image']['name'])) {
            $up = new fileDir('../upload/slider/');
            $slide_image = $up->upload($_FILES['image']);
        } else {
            $slide_image = make_safe($request['slide_image']);
        }
        if (empty($title)) {
            $message = notification('warning','Insert The Slide Title Please.');
        } elseif (empty($slide_image)) {
            $message = notification('warning','Please upload slide image.');
        } else {
            $sql = "UPDATE ss_slider SET title='$title',slide_title='$slide_title',slide_text='$slide_text',slide_image='$slide_image' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $message = notification('success','Slide is Edited Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function slide($id)
    {
        $sql = "SELECT * FROM ss_slider WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function slides()
    {
        $sql = "SELECT * FROM ss_slider ORDER BY slide_order ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            while($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }




}