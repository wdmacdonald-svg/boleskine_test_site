$connectionString = "Server=127.0.0.1;Database=boleskine_db;Uid=root;Pwd=;"
$query1 = "SELECT post_content FROM wp_posts WHERE ID = 14;"
$query2 = "SELECT post_content FROM wp_posts WHERE ID = 984;"

# We will just run mysql command line since it's easier and doesn't require .NET drivers if they aren't loaded
$header = & "D:\My_Websites\xampp\mysql\bin\mysql.exe" -u root -s -N -e "SELECT post_content FROM wp_posts WHERE ID = 14;" boleskine_db
$custom2 = & "D:\My_Websites\xampp\mysql\bin\mysql.exe" -u root -s -N -e "SELECT post_content FROM wp_posts WHERE ID = 984;" boleskine_db

# The header string contains two main blocks: the wp:group (navigation/logo) and wp:cover (image)
# We want to take the wp:group from Header (14) and combine it with the wp:cover from Custom2 (984)

$headerGroup = $header.Substring(0, $header.IndexOf("<!-- wp:cover"))
$custom2Cover = $custom2.Substring($custom2.IndexOf("<!-- wp:cover"))

$newCustom2 = $headerGroup + $custom2Cover

# Escape single quotes for SQL
$newCustom2Escaped = $newCustom2 -replace "'", "''"

$updateQuery = "UPDATE wp_posts SET post_content = '$newCustom2Escaped' WHERE ID = 984;"
& "D:\My_Websites\xampp\mysql\bin\mysql.exe" -u root -e $updateQuery boleskine_db

Write-Output "Successfully updated Header Custom2"
