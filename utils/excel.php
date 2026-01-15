<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excel
{
	public static function create_from_table(array $data, $fileName = 'data.xlsx')
	{
		$headers = $data["headers"] ?? [];
		$fields = $data["fields"] ?? [];
		$footers = $data["foot"] ?? [];
		$content = $data["content"];

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		if(!empty($data["title"]))
		{
			$sheet->setCellValue([1, 1], $data["title"]);
			$sheet->getStyle("A1")
				->getFont()
				->setSize(16);
			$sheet->getRowDimension('1')
				->setRowHeight(20);
			$sheet->mergeCells("A1:" . chr(64 + count($headers)) . "1");
			$sheet->getStyle('A1')
				->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		}

		for ($i = 0, $l = sizeof($headers); $i < $l; $i++)
		{
			$sheet->setCellValue([$i + 1, 3], $headers[$i]);
		}
		$sheet->getStyle("A3:" . chr(64 + count($headers)) . "3")
			->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('d9d9d9');

		$sheet->getStyle("A3:" . chr(64 + count($headers)) . "3")
			->getFont()
			->setBold(true);

		$maxWidth = Array();
		$l = sizeof($content);
		for ($i = 0; $i < $l; $i++)
		{
			$j = 0;
			foreach ($fields as $k)
			{
				$v = $content[$i][$k] ?? "";
				if(strlen($v) > 100)
				{
					$maxWidth[$j] = 50;
				}
				$sheet->setCellValue([$j + 1, $i + 4], $v);
				$j++;
			}
		}
		if(count($footers) > 0)
		{
			$j = 0;
			foreach ($fields as $k)
			{
				$v = $footers[$k] ?? "";
				if(strlen($v) > 100)
				{
					$maxWidth[$j] = 50;
				}
				$sheet->setCellValue([$j + 1, $l + 4], $v);
				$j++;
			}

			$sheet->getStyle("A" . ($l + 4) . ":" . chr(64 + count($fields)) . ($l + 4))
				->getFont()
				->setBold(true);
		}

		$sheet->calculateColumnWidths();
		foreach (range(65, 64 + count($fields)) as $ascii)
		{
			$dimension = $sheet->getColumnDimension(chr($ascii));
			if(isset($maxWidth[$ascii - 65]))
			{
				$dimension->setWidth($maxWidth[$ascii - 65]);
			}
			else
			{
				$dimension->setAutoSize(true);
			}
		}
		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
		$writer->save('php://output');
	}
}
?>
