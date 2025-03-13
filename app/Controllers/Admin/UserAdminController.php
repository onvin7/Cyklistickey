<?php

namespace App\Controllers\Admin;

use App\Models\User;

use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromgif;

class UserAdminController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new User($db);
    }

    // Zobrazení seznamu uživatelů
    public function index()
    {
        $sortBy = $_GET['sort_by'] ?? 'id';      // Výchozí řazení podle ID
        $order = $_GET['order'] ?? 'ASC';       // Výchozí vzestupné řazení
        $filter = $_GET['filter'] ?? '';        // Výchozí bez filtru

        // Načtení uživatelů s filtrováním a řazením
        $users = $this->model->getAllWithSortingAndFiltering($sortBy, $order, $filter);

        // Zobrazení view
        $view = '../../app/Views/Admin/users/index.php';
        include '../../app/Views/Admin/layout/base.php';
    }


    public function edit($id)
    {
        // Načtení uživatele z databáze
        $user = $this->model->getById($id);

        // Kontrola, zda byl uživatel nalezen
        if (!$user) {
            echo "Uživatel nenalezen.";
            return;
        }

        // Zahrnutí šablony pro úpravu uživatele
        $view = '../../app/Views/Admin/users/edit.php';
        include '../../app/Views/Admin/layout/base.php';
    }

    public function update($id, $postData)
    {
        if (empty($postData['email']) || empty($postData['name']) || empty($postData['surname'])) {
            echo "E-mail, jméno a příjmení jsou povinné.";
            return;
        }

        $data = [
            'id' => $id,
            'email' => $postData['email'],
            'name' => $postData['name'],
            'surname' => $postData['surname'],
            'role' => $postData['role'] ?? 0,
            'profil_foto' => $postData['profil_foto'] ?? null,
            'zahlavi_foto' => $postData['zahlavi_foto'] ?? null,
            'popis' => $postData['popis'] ?? ''
        ];

        $result = $this->model->update($data);

        if ($result) {
            header("Location: /admin/users");
            exit;
        } else {
            echo "Chyba při aktualizaci uživatele.";
        }
    }

    public function delete($id)
    {
        $result = $this->model->delete($id); // Volání metody `delete` v modelu

        if ($result) {

            header("Location: /admin/users");
            exit;
        } else {
            echo "Chyba při mazání uživatele.";
        }
    }



    public function settings()
    {
        $userId = $_SESSION['user_id'];

        $user = $this->model->getById($userId);
        $social_links = $this->model->getUserSocialLinks($userId);
        $available_socials = $this->model->getAvailableSocialSites();

        $view = '../../app/Views/Admin/users/settings.php';
        include '../../app/Views/Admin/layout/base.php';
    }

    public function updateSettings()
    {
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $description = $_POST['description'];

        error_log("DEBUG: Příchozí soubory: " . print_r($_FILES, true));

        // **Profilová fotka**
        $profile_photo = $_SESSION['profile_photo'] ?? null;
        if (isset($_FILES['profil_foto']) && $_FILES['profil_foto']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../../../web/uploads/users/thumbnails/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['profil_foto']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['profil_foto']['tmp_name'], $targetFile)) {
                error_log("✅ Profilová fotka nahrána do: $targetFile");
                $this->resizeAndCropImage($targetFile, 400, 400, true);
                $_SESSION['profile_photo'] = $fileName; // Uložení do session
            } else {
                error_log("✖️ Chyba při přesunu profilové fotky.");
            }
        }

        // **Background fotka (kontrola na prázdnou hodnotu)**
        $header_photo = $_SESSION['header_photo'] ?? null;
        if (isset($_FILES['zahlavi_foto']) && $_FILES['zahlavi_foto']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../../../web/uploads/users/background/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['zahlavi_foto']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['zahlavi_foto']['tmp_name'], $targetFile)) {
                error_log("✅ Background fotka nahrána do: $targetFile");
                $this->resizeAndCropImage($targetFile, 1200, 675, false);
                $_SESSION['header_photo'] = $fileName; // Uložení do session
            } else {
                error_log("✖️ Chyba při přesunu background fotky.");
            }
        }

        // **Aktualizace databáze pouze s novými hodnotami**
        $this->model->updateUser($userId, $name, $surname, $email, $description, $_SESSION['profile_photo'], $_SESSION['header_photo']);

        $social_ids = $_POST['social_id'] ?? [];
        $links = $_POST['link'] ?? [];

        error_log("DEBUG: Social ID - " . print_r($social_ids, true));
        error_log("DEBUG: Links - " . print_r($links, true));

        if (!empty($social_ids) && !empty($links)) {
            $this->model->updateUserSocialLinks($userId, $social_ids, $links);
        } else {
            error_log("✖️ Žádná sociální síť nebyla odeslána.");
        }

        $_SESSION['success'] = "Nastavení bylo úspěšně aktualizováno.";
        header("Location: /admin/settings");
        exit();
    }

    private function resizeAndCropImage($filePath, $maxWidth, $maxHeight, $isSquare)
    {
        list($originalWidth, $originalHeight, $imageType) = getimagesize($filePath);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filePath);
                break;
            default:
                return false;
        }

        if ($isSquare) {
            // **Ořez na čtverec podle menší strany**
            $size = min($originalWidth, $originalHeight);
            $srcX = ($originalWidth - $size) / 2;
            $srcY = ($originalHeight - $size) / 2;
            $croppedImage = imagecreatetruecolor($size, $size);
            imagecopyresampled($croppedImage, $source, 0, 0, $srcX, $srcY, $size, $size, $size, $size);
        } else {
            // **Ořez na 16:9, pokud je větší než limit, zmenšit**
            $targetWidth = min($originalWidth, $maxWidth);
            $targetHeight = ($targetWidth / 16) * 9;

            if ($targetHeight > $originalHeight) {
                $targetHeight = min($originalHeight, $maxHeight);
                $targetWidth = ($targetHeight / 9) * 16;
            }

            $srcX = ($originalWidth - $targetWidth) / 2;
            $srcY = ($originalHeight - $targetHeight) / 2;
            $croppedImage = imagecreatetruecolor($targetWidth, $targetHeight);
            imagecopyresampled($croppedImage, $source, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $targetWidth, $targetHeight);
        }

        // **Změna velikosti na maximální rozměr**
        $resizedImage = imagecreatetruecolor($maxWidth, $maxHeight);
        imagecopyresampled($resizedImage, $croppedImage, 0, 0, 0, 0, $maxWidth, $maxHeight, imagesx($croppedImage), imagesy($croppedImage));

        // **Uložit obrázek zpět**
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($resizedImage, $filePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($resizedImage, $filePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($resizedImage, $filePath);
                break;
        }

        imagedestroy($source);
        imagedestroy($croppedImage);
        imagedestroy($resizedImage);
    }

    public function socialSites()
    {
        $socials = $this->model->getAllSocialSites();
        $view = '../../app/Views/Admin/users/social_sites.php';
        include '../../app/Views/Admin/layout/base.php';
    }

    public function saveSocialSite()
    {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'];
        $url = $_POST['url'];
        $icon = $_POST['current_icon'] ?? null;

        // Nahrávání ikony
        if (!empty($_FILES['icon']['name']) && $_FILES['icon']['error'] === 0) {
            $uploadDir = __DIR__ . '/../../../web/uploads/social_icons/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['icon']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetFile)) {
                $icon = $fileName;
            }
        }

        if ($id) {
            $this->model->updateSocialSite($id, $name, $url, $icon);
        } else {
            $this->model->addSocialSite($name, $url, $icon);
        }

        $_SESSION['success'] = "Sociální síť byla úspěšně uložena.";
        header("Location: /admin/social-sites");
        exit();
    }

    public function deleteSocialSite($id)
    {
        $this->model->deleteSocialSite($id);

        $_SESSION['success'] = "Sociální síť byla odstraněna.";
        header("Location: /admin/social-sites");
        exit();
    }
}
