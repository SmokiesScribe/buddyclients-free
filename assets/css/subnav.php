<style>
    /* CSS for horizontal display of subnavigation links */
    .bc_subnav {
        display: flex;
        list-style-type: none;
        padding: 0;
        margin-bottom: 20px;
    }
    
    .bc_subnav a {
        display: inline-block;
        padding: 6px 16px;
        text-decoration: none;
        font-size: 16px;
        color: #333;
        border-radius: 5px;
        border: solid 1px #e7e9ec;
        background-color: #fafbfd;
    }
    
    .bc_subnav a:hover,
    .bc_subnav a.active {
        background-color: #fff;
        color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    }
</style>