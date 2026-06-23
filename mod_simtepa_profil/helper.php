<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simtepa_profil
 */

defined('_JEXEC') or die;

class ModSimtepaProfilHelper
{
    public static function getPegawai($params)
    {
        $jsonUrl = trim((string) $params->get('json_url', ''));
        $cacheTtl = max(300, (int) $params->get('cache_ttl', 3600));

        if ($jsonUrl === '') {
            return array();
        }

        $cacheFile = JPATH_CACHE . '/mod_simtepa_profil.json';
        $hasFreshCache = is_file($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl);

        if ($hasFreshCache) {
            $json = file_get_contents($cacheFile);
        } else {
            $json = self::fetchUrl($jsonUrl);

            if ($json && is_writable(JPATH_CACHE)) {
                file_put_contents($cacheFile, $json, LOCK_EX);
            }

            if (!$json && is_file($cacheFile)) {
                $json = file_get_contents($cacheFile);
            }
        }

        $data = $json ? json_decode($json, true) : array();

        return is_array($data) ? $data : array();
    }

    public static function escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        $timestamp = strtotime($date);

        if (!$timestamp) {
            return self::escape($date);
        }

        $months = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        );

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    public static function photoUrl($filename)
    {
        if (!$filename) {
            return '';
        }

        return 'https://simtepa.mahkamahagung.go.id/dokumen/file_edoc?folder=fotoPegawai&filename=' . rawurlencode($filename);
    }

    public static function lhkpnUrl($filename)
    {
        if (!$filename) {
            return '';
        }

        return 'https://simtepa.mahkamahagung.go.id/dokumen/file_edoc?folder=folderBuktiKirim&filename=' . rawurlencode($filename);
    }

    public static function cleanHistory($html)
    {
        $html = strip_tags((string) $html, '<ol><ul><li><br><b><strong><i><em>');
        $html = preg_replace('/\s+style=("|\')[^"\']*("|\')/i', '', $html);
        $html = preg_replace('/\s+on[a-z]+=("|\')[^"\']*("|\')/i', '', $html);

        return $html ?: '<span class="simtepa-muted">Belum ada data.</span>';
    }

    private static function fetchUrl($url)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 mod_simtepa_profil',
            ));

            $response = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($response !== false && $status >= 200 && $status < 300) {
                return $response;
            }
        }

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 15,
                'header' => "User-Agent: Mozilla/5.0 mod_simtepa_profil\r\n",
            ),
        ));

        $response = @file_get_contents($url, false, $context);

        return $response === false ? null : $response;
    }
}
