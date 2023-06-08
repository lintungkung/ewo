from pikepdf import Pdf,Encryption
import argparse

parser = argparse.ArgumentParser()
parser.add_argument("--file1-path")
parser.add_argument("--file2-path", default=None)
parser.add_argument("--file3-path", default=None)
parser.add_argument("--file4-path", default=None)
parser.add_argument("--file5-path", default=None)
parser.add_argument("--file6-path", default=None)
parser.add_argument("--output-path")
parser.add_argument("--password")
args = parser.parse_args()

pdf = Pdf.new()
# file1
src = Pdf.open(args.file1_path)
pdf.pages.extend(src.pages)

# file 借用單，取回單
if args.file6_path:
    src = Pdf.open(args.file6_path)
    pdf.pages.extend(src.pages)

# file dtv 
if args.file2_path:
    src = Pdf.open(args.file2_path)
    pdf.pages.extend(src.pages)
    
# file dtv pcl
if args.file3_path:
    src = Pdf.open(args.file3_path)
    pdf.pages.extend(src.pages)
    
# file cm
if args.file4_path:
    src = Pdf.open(args.file4_path)
    pdf.pages.extend(src.pages)
    
# file cm pcl
if args.file5_path:
    src = Pdf.open(args.file5_path)
    pdf.pages.extend(src.pages)

pdf.save(args.output_path, encryption=Encryption(user=args.password, owner=args.password))
