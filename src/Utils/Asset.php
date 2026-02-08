<?php
function queryAssetWithVersion($path)
{
//   $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
//   if (file_exists($fullPath)) {
//     return $path . '?v=natk';
//   }
  return $path . '?v=natk'; // fallback nếu file không tồn tại
}
