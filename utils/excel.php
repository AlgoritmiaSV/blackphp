<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excel
{
	public static function create_from_table(array $data, $fileName = 'data.xlsx')
	{
		$headers = empty($data["headers"]) ? Array() : $data["headers"];
		$fields = empty($data["fields"]) ? Array() : $data["fields"];
		$content = $data["content"];

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		if(!empty($data["title"]))
		{
			$sheet->setCellValueByColumnAndRow(1, 1, $data["title"]);
			$sheet->getStyle("A1")->getFont()->setSize(16);
			$sheet->mergeCells("A1:" . chr(64 + count($headers)) . "1");
			$sheet->getStyle('A1')
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		}

		for ($i = 0, $l = sizeof($headers); $i < $l; $i++)
		{
			$sheet->setCellValueByColumnAndRow($i + 1, 3, $headers[$i]);
		}
		$sheet->getStyle("A3:" . chr(64 + count($headers)) . "3")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('d9d9d9');

		for ($i = 0, $l = sizeof($content); $i < $l; $i++)
		{
			$j = 0;
			foreach ($fields as $k)
			{
				$v = empty($content[$i][$k]) ? "" : $content[$i][$k];
				$sheet->setCellValueByColumnAndRow($j + 1, $i + 4, $v);
				$j++;
			}
		}

		foreach (range(65, 64 + count($fields)) as $ascii)
		{
			$spreadsheet->getActiveSheet()->getColumnDimension(chr($ascii))->setAutoSize(true);
		}
		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
		$writer->save('php://output');
	}
}
?>
