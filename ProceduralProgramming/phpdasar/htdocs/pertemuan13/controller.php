<?php
/**
 * @param string $query
 * @return array
 */
function select(string $query): array {
    global $link;
    $result = mysqli_query($link, htmlspecialchars(trim($query)));
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * @param string $keyword
 * @return array
 */
function search(string $keyword): array {
    $query = "SELECT * FROM mahasiswa WHERE nama LIKE '%$keyword%' OR nim LIKE '%$keyword%' OR jurusan LIKE '%$keyword%' OR angkatan LIKE '%$keyword%'";
    return select($query);
}

/**
 * @param array $data
 * @return int
 */
function insert(array $data): int {
    global $link;
    $nim = mysqli_real_escape_string($link, htmlspecialchars(trim($data['nim'])));
    $nama = mysqli_real_escape_string($link, htmlspecialchars(trim($data['nama'])));
    $jurusan = mysqli_real_escape_string($link, htmlspecialchars(trim($data['jurusan'])));
    $angkatan = mysqli_real_escape_string($link, htmlspecialchars(trim($data['angkatan'])));
    $foto = upload();
    if (!$foto): return false; endif;

    $query = "INSERT INTO mahasiswa (nama, nim, jurusan, angkatan, foto) VALUES ('$nama','$nim','$jurusan', $angkatan, '$foto')";
    mysqli_query($link, $query);
    return mysqli_affected_rows($link);
}

/**
 * @return string
 */
function upload(): string {
    $filename = $_FILES['foto']['name'];
    $filesize = $_FILES['foto']['size'];
    $errorfile = $_FILES['foto']['error'];
    $tmpname = $_FILES['foto']['tmp_name'];
    if ($errorfile === 4):
        throw new \RuntimeException("Gagal mengupload gambar karena kesalahan yang tidak diketahui!", 1);
    endif;

    $validextension = ['jpg', 'jpeg', 'png', 'svg'];
    $extensionfile = explode('.', $filename);
    $arrayreverse = array_reverse($extensionfile);
    $prefixfilename = strtolower(end($arrayreverse));
    $extensionfile = strtolower(end($extensionfile));
    if (!in_array($extensionfile, $validextension)):
        throw new \RuntimeException("Yang kamu masukkan bukan gambar!", 1);
    endif;
    if ($filesize > 1000000):
        throw new \RuntimeException("Ukuran gambar kamu terlalu besar!", 1);
    endif;

    $newfilename = uniqid($prefixfilename, true) . ".$extensionfile";
    move_uploaded_file($tmpname, "img/$newfilename");
    return $newfilename;
}

/**
 * @param int $id
 * @param array $data
 * @return int
 */
function update(int $id, array $data): int {
    global $link;
    $nim = mysqli_real_escape_string($link, htmlspecialchars(trim($data['nim'])));
    $nama = mysqli_real_escape_string($link, htmlspecialchars(trim($data['nama'])));
    $jurusan = mysqli_real_escape_string($link, htmlspecialchars(trim($data['jurusan'])));
    $angkatan = mysqli_real_escape_string($link, htmlspecialchars(trim($data['angkatan'])));
    $fotolama = mysqli_real_escape_string($link, htmlspecialchars($data['fotolama']));
    if ($_FILES['foto']['error'] === 4) {
        $fotobaru = $fotolama;
    } else {
        $fotobaru = upload();
    }
    $query = "UPDATE mahasiswa SET nama ='$nama', nim = '$nim', jurusan = '$jurusan', angkatan = $angkatan, foto = '$fotobaru' WHERE id = $id";
    mysqli_query($link, $query);
    return mysqli_affected_rows($link);
}

/**
 * @param $id
 * @return int
 */
function delete(int $id): int {
    global $link;
    $query = "DELETE FROM mahasiswa WHERE id = $id";
    mysqli_query($link, $query);
    return mysqli_affected_rows($link);
}