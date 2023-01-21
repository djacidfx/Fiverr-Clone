<?php
class Services extends General {

    function add_service($request) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        if (!empty($request['slug'])) {
            $slug = $this->make_service_slug(slugit(make_safe(xss_clean(htmlspecialchars($request['slug'],ENT_QUOTES)))),0);
        } else {
            $slug = $this->make_service_slug(slugit($title),0);
        }
        $category_id = make_safe((int) $request['category']);
        $content = htmlentities(htmlspecialchars($request['content'],ENT_QUOTES));
        $price = htmlspecialchars($request['price'],ENT_QUOTES);
        $delivery = htmlspecialchars($request['delivery'],ENT_QUOTES);
        $requirements = htmlentities(htmlspecialchars($request['requirements'],ENT_QUOTES));
        $seo_description = strip_tags(htmlspecialchars($request['seo_description'],ENT_QUOTES));
        $active = make_safe((int) $request['active']);
        $digital_download = make_safe((int) $request['digital_download']);
        if (empty($seo_description)) {
            $seo_description = mb_substr(strip_tags(htmlspecialchars_decode(html_entity_decode($content, ENT_QUOTES),ENT_QUOTES)),0,255,'UTF-8');
        }
        $seo_keywords = make_safe($request['seo_keywords']);
        if (empty($seo_keywords)) {
            $seo_keywords = $this->generate_keywords($title);
        }
        if ($digital_download == 1) {
            if (!empty($_FILES['digital_download_file']['name'])) {
                $up = new fileDir('../upload/digital/');
                $digital_download_file = $up->upload($_FILES['digital_download_file']);
            } else {
                $digital_download_file = '';
            }
        } else {
            $digital_download_file = '';
        }

        if (empty($title)) {
            $message = notification('warning','Insert The Title Please.');
        } elseif (empty($content)) {
            $message = notification('warning','Write Some Details Please.');
        } elseif (empty($price)) {
            $message = notification('warning','Insert Service Price please');
        } else {
            $sql = "INSERT INTO ss_services (title,slug,category_id,content,requirements,price,delivery,seo_keywords,seo_description,active,deleted,digital_download,digital_download_file) VALUES ('$title','$slug','$category_id','$content','$requirements','$price','$delivery','$seo_keywords','$seo_description','$active','0','$digital_download','$digital_download_file')";
            $query = $this->db->query($sql);
            if ($query) {
                $result_id = $this->db->insert_id;
                $images = $this->service_tmp_images($_SESSION['admin_session_id']);
                if ($images != 0) {
                    foreach ($images AS $image) {
                        mkdir('../upload/services/'.$result_id,0755);
                        rename("../upload/tmp_images/".$image['filename'], "../upload/services/".$result_id."/".$image['filename']);
                        if (file_exists("../upload/tmp_images/".$image['filename'])) {
                            unlink("../upload/tmp_images/".$image['filename']);
                        }
                        $this->db->query("INSERT INTO ss_services_images (service_id,filename) VALUES ('$result_id','$image[filename]')");
                        $this->db->query("DELETE FROM ss_services_images_temp WHERE session_id='$_SESSION[admin_session_id]' AND filename='$image[filename]'");

                    }
                }
                unset($_SESSION['user_session_id']);
                session_regenerate_id();
                $message = notification('success','Services is Added Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function generate_keywords($text) {
        $arr = explode(" ",$text);
        $keywords = array();
        foreach ($arr AS $tag) {
            if (mb_strlen($tag,'UTF-8') > 3) {
                array_push($keywords,$tag);
            }
        }
        return implode(',',$keywords);
    }

    function edit_service($request,$id) {
        $title = make_safe(xss_clean(htmlspecialchars($request['title'],ENT_QUOTES)));
        $content = htmlentities(htmlspecialchars($request['content'],ENT_QUOTES));
        $price = htmlspecialchars($request['price'],ENT_QUOTES);
        $delivery = htmlspecialchars($request['delivery'],ENT_QUOTES);
        $requirements = htmlentities(htmlspecialchars($request['requirements'],ENT_QUOTES));
        $active = make_safe((int) $request['active']);
        $digital_download = make_safe((int) $request['digital_download']);
        $category_id = make_safe((int) $request['category']);
        if (!empty($request['slug'])) {
            $slug = $this->make_service_slug(slugit(make_safe(xss_clean(htmlspecialchars($request['slug'],ENT_QUOTES)))),$id);
        } else {
            $slug = $this->make_service_slug(slugit($title),$id);
        }
        $seo_description = strip_tags(htmlspecialchars($request['seo_description'],ENT_QUOTES));
        if (empty($seo_description)) {
            $seo_description = mb_substr(strip_tags(htmlspecialchars_decode(html_entity_decode($content,ENT_QUOTES),ENT_QUOTES)),0,255,'UTF-8');
        }
        $seo_keywords = make_safe($request['seo_keywords']);
        if (empty($seo_keywords)) {
            $seo_keywords = $this->generate_keywords($title);
        }
        if ($digital_download == 1) {
            if (!empty($_FILES['digital_download_file']['name'])) {
                $up = new fileDir('../upload/digital/');
                $digital_download_file = $up->upload($_FILES['digital_download_file']);
            } else {
                $digital_download_file = make_safe($request['current_digital_download_file']);
            }
        } else {
            $digital_download_file = make_safe($request['current_digital_download_file']);
        }
        if (empty($title)) {
            $message = notification('warning','Insert The Title Please.');
        } elseif (empty($content)) {
            $message = notification('warning','Write Some Details Please.');
        } elseif (empty($price)) {
            $message = notification('warning','Insert Service Price please');
        } else {
            $sql = "UPDATE ss_services SET title='$title',slug='$slug',category_id='$category_id',content='$content',requirements='$requirements',price='$price',delivery='$delivery',seo_keywords='$seo_keywords',seo_description='$seo_description',active='$active',digital_download='$digital_download',digital_download_file='$digital_download_file' WHERE id='$id'";
            $query = $this->db->query($sql);
            if ($query) {
                $images = $this->service_tmp_images($_SESSION['admin_session_id']);
                if ($images != 0) {
                    $result_id = $id;
                    foreach ($images AS $image) {
                        mkdir('../upload/services/'.$result_id,0755);
                        rename("../upload/tmp_images/".$image['filename'], "../upload/services/".$result_id."/".$image['filename']);
                        if (file_exists("../upload/tmp_images/".$image['filename'])) {
                            unlink("../upload/tmp_images/".$image['filename']);
                        }
                        $this->db->query("INSERT INTO ss_services_images (service_id,filename) VALUES ('$result_id','$image[filename]')");
                        $this->db->query("DELETE FROM ss_services_images_temp WHERE session_id='$_SESSION[admin_session_id]' AND filename='$image[filename]'");

                    }
                }
                unset($_SESSION['admin_session_id']);
                session_regenerate_id();
                $message = notification('success','Services is Edited Successfully.');
            } else {
                $message = notification('danger','Error Happened.');
            }
        }
        return $message;
    }

    function service($id)
    {
        $sql = "SELECT * FROM ss_services WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function service_tmp_images($session) {
        $sql = "SELECT * FROM ss_services_images_temp WHERE session_id='$session'";
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

    function service_images($service_id) {
        $sql = "SELECT * FROM ss_services_images WHERE service_id='$service_id'";
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

    function service_image($id)
    {
        $sql = "SELECT * FROM ss_services_images WHERE id='$id' LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return 0;
        } else {
            $row = $query->fetch_assoc();
            return $row;
        }
    }

    function make_service_slug($slug,$id)
    {
        if ($id == 0) {
            $sql = "SELECT * FROM ss_services WHERE slug='$slug' LIMIT 1";
        } else {
            $sql = "SELECT * FROM ss_services WHERE slug='$slug' AND id!='$id' LIMIT 1";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows == 0) {
            return $slug;
        } else {
            return $slug.'-'.$query->num_rows;
        }
    }


}