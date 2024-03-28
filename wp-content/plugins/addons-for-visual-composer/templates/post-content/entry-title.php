<?php

$title = the_title('<' . $title_tag . ' class="lvca-post-title"><a href="' . get_permalink() . '" title="' . get_the_title() . '"
                                               rel="bookmark"' . $target . '>', '</a></' . $title_tag . '>', false);

/* If there's no post title, return a default title */
if (empty($title))
    $title = '<' . $title_tag . ' class="lvca-post-title lvca-no-entry-title"><a href="' . get_permalink() . '" rel="bookmark"' . $target . '>' . esc_html__('(Untitled)',
            'livemesh-vc-addons') . '</a></' . $title_tag . '>';

echo wp_kses_post($title);