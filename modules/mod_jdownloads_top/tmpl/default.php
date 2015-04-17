<?php
/**
* @version $Id: mod_jdownloads_top.php
* @package mod_jdownloads_top
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*
* This modul shows you the most recent downloads from the jDownloads component. 
* It is only for jDownloads 2.5 and later (Support: www.jDownloads.com)
*/

defined('_JEXEC') or die;

    $html = '';
	
    if ($files){
    	$html = '<table width="100%" class="moduletable'.$moduleclass_sfx.'">';
		
        if ($text_before <> ''){
			$html .= '<tr><td>'.$text_before.'</td></tr>';   
		}
		
        for ($i=0; $i<count($files); $i++) {
            $has_no_file = false;
            if (!$files[$i]->url_download && !$files[$i]->other_file_id && !$files[$i]->extern_file){
               // only a document without file
               $has_no_file = true;           
            }
                         
            // get the first image as thumbnail when it exist           
            $thumbnail = ''; 
            $first_image = '';
            $images = explode("|",$files[$i]->images);
            if (isset($images[0])) $first_image = $images['0'];

            $version = $params->get('short_version', ''); 

            // short the file title?			
            if ($sum_char > 0){
				$gesamt = strlen($files[$i]->file_title) + strlen($files[$i]->release) + strlen($short_version) +1;
				if ($gesamt > $sum_char){
				   $files[$i]->file_title = JString::substr($files[$i]->file_title, 0, $sum_char).$short_char;
				   $files[$i]->release = '';
				}    
			} 
			
            // get for every item the menu link itemid when exists 
			$db->setQuery("SELECT id from #__menu WHERE link = 'index.php?option=com_jdownloads&view=category&catid=".$files[$i]->cat_id."' and published = 1");
			$Itemid = $db->loadResult();
			if (!$Itemid){
				$Itemid = $root_itemid;
			}  

            // create the viewed category text   			
            if ($cat_show) {
				if ($cat_show_type == 'containing') {
					$db->setQuery('SELECT title FROM #__jdownloads_categories WHERE id = '.$files[$i]->cat_id);
					$cattitle = $db->loadResult();
					$cat_show_text2 = $cat_show_text.$cattitle;
				} else {
					$db->setQuery('SELECT cat_dir FROM #__jdownloads_categories WHERE id = '.$files[$i]->cat_id);
					$catdir = $db->loadResult();
					$cat_show_text2 = $cat_show_text.$catdir;
				}
			} else {
				$cat_show_text2 = '';
			}    

            // create the link
            if ($files[$i]->link == '-'){
                // the user have the access to view this item
                if ($detail_view == '1'){
                    if ($detail_view_config == 0){                    
                        // the details view is deactivated in jD config so the
                        // link must start directly the download process
                        if ($direct_download_config == 1){
                            if (!$has_no_file){
                                $link = JRoute::_('index.php?option='.$option.'&amp;task=download.send&amp;id='.$files[$i]->slug.'&amp;catid='.$files[$i]->cat_id.'&amp;m=0');                    
                            } else {
                                // create a link to the Downloads category as this download has not a file
                                $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                            }   
                        } else {
                            // link to the summary page
                            if (!$has_no_file){
                                $link = JRoute::_('index.php?option='.$option.'&amp;view=summary&amp;id='.$files[$i]->slug.'&amp;catid='.$files[$i]->cat_id);
                            } else {
                                // create a link to the Downloads category as this download has not a file
                                $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                            }   
                        }    
                    } else {
                        // create a link to the details view
                        $link = JRoute::_('index.php?option='.$option.'&amp;view=download&id='.$files[$i]->slug.'&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);                    
                    }                       
			    } else {    
				    // create a link to the Downloads category
                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
			    }    
            } else {
                $link = $files[$i]->link;
            }
            if (!$files[$i]->release) $version = '';
			
			// add mime file pic
            $size = 0;
			$files_pic = '';
			$number = '';
			if ($view_pics){
				$size = (int)$view_pics_size;
				$files_pic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" align="top" width="'.$size.'" height="'.$size.'" border="0" alt="" /> '; 
			}
			if ($view_numerical_list){
				$num = $i+1;
				$number = "$num. ";
			}    
			
            // add description in tooltip 
            if ($view_tooltip && $files[$i]->description != ''){
				$link_text = '<a href="'.$link.'">'.JHtml::tooltip(strip_tags(substr($files[$i]->description,0,$view_tooltip_length)).$short_char,JText::_('MOD_JDOWNLOADS_TOP_DESCRIPTION_TITLE'),$files[$i]->file_title.' '.$version.$files[$i]->release,$files[$i]->file_title.' '.$version.$files[$i]->release).'</a>';                
			} else {    
				$link_text = '<a href="'.$link.'">'.$files[$i]->file_title.' '.$version.$files[$i]->release.'</a>';
			}    
            
            $html .= '<tr valign="top"><td align="'.$alignment.'">'.$number.$files_pic.$link_text.'</td>';
            
			// add the hits
            if ($view_hits) {
				if ($files[$i]->downloads){
					if ($view_hits_same_line){
						$html .= '<td align="'.$hits_alignment.'">'.$hits_label.'&nbsp;'.modJdownloadsTopHelper::strToNumber($files[$i]->downloads).'</td>'; 
					} else {
						$html .= '<tr valign="top"><td align="'.$hits_alignment.'">'.$hits_label.'&nbsp;'.modJdownloadsTopHelper::strToNumber($files[$i]->downloads).'</td>';
					}
				}    
			} 
			$html .= '</tr>';

            // add the first download screenshot when exists and activated in options
            if ($view_thumbnails){
                if ($first_image){
                    $thumbnail = '<img class="img" src="'.$thumbfolder.$first_image.'" align="top" style="padding:5px;" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" border="'.$border.'" alt="'.$files[$i]->file_title.'" />';
                } else {
                    // use placeholder
                    if ($view_thumbnails_dummy){
                        $thumbnail = '<img class="img" src="'.$thumbfolder.'no_pic.gif" align="top" style="padding:5px;" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" border="'.$border.'" alt="" />';    
                    }
                }
                if ($thumbnail) $html .= '<tr valign="top"><td align="'.$alignment.'">'.$thumbnail.'</td></tr>';
            } 
			
			// add category info 
            if ($cat_show_text2) {
				if ($cat_show_as_link){
					$html .= '<tr valign="top"><td align="'.$alignment.'" style="font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid.'">'.$cat_show_text2.'</a></td></tr>';
				} else {    
					$html .= '<tr valign="top"><td align="'.$alignment.'" style="font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';">'.$cat_show_text2.'</td></tr>';
				}
			}    
		}
		if ($text_after <> ''){
			$html .= '<tr><td>'.$text_after.'</td></tr>';
		}
        echo $html.'</table>';
    }
?>