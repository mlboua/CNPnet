import os
import sys
from PyPDF2 import PdfFileReader, PdfFileMerger

#files_dir = "D:\\wamp\\www\\generations\\temp"
files_dir = sys.argv[1]
ouputPdf = sys.argv[2]

pdf_files = [f for f in os.listdir(files_dir) if f.endswith("pdf")]
merger = PdfFileMerger()
pdf_files.sort()

for filename in pdf_files:
	merger.append(PdfFileReader(os.path.join(files_dir, filename), "rb"))
merger.write(os.path.join(files_dir, ouputPdf))