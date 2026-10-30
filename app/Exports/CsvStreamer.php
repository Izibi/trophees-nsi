<?php

namespace App\Exports;

/**
 * Shared CSV streaming response: same headers and `fputcsv` behaviour as the
 * legacy outputFile() helpers duplicated across the old controllers.
 */
class CsvStreamer
{
    /**
     * Streams a CSV attachment built from an iterable of rows (each row being
     * an array of cell values, in the same order as $header).
     *
     * @param string        $filename
     * @param string[]      $header CSV header labels
     * @param iterable      $rows   generator or array of rows
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function stream($filename, array $header, $rows)
    {
        // Dated download: trophees_nsi_users_20261030.csv
        $filename = preg_replace('/\.csv$/i', '_' . date('Ymd') . '.csv', $filename);

        $callback = function () use ($header, $rows) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, $header);
            foreach ($rows as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
        };

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream($callback, 200, $headers);
    }
}
