const fs = require('fs');
const zlib = require('zlib');
const https = require('https');
const path = require('path');

function encode64(data) {
  let r = "";
  for (let i = 0; i < data.length; i += 3) {
    if (i + 2 === data.length) {
      r += append3bytes(data.charCodeAt(i), data.charCodeAt(i + 1), 0);
    } else if (i + 1 === data.length) {
      r += append3bytes(data.charCodeAt(i), 0, 0);
    } else {
      r += append3bytes(data.charCodeAt(i), data.charCodeAt(i + 1), data.charCodeAt(i + 2));
    }
  }
  return r;
}

function append3bytes(b1, b2, b3) {
  let c1 = b1 >> 2;
  let c2 = ((b1 & 0x3) << 4) | (b2 >> 4);
  let c3 = ((b2 & 0xF) << 2) | (b3 >> 6);
  let c4 = b3 & 0x3F;
  let r = "";
  r += encode6bit(c1 & 0x3F);
  r += encode6bit(c2 & 0x3F);
  r += encode6bit(c3 & 0x3F);
  r += encode6bit(c4 & 0x3F);
  return r;
}

function encode6bit(b) {
  if (b < 10) return String.fromCharCode(48 + b);
  b -= 10;
  if (b < 26) return String.fromCharCode(65 + b);
  b -= 26;
  if (b < 26) return String.fromCharCode(97 + b);
  b -= 26;
  if (b === 0) return '-';
  if (b === 1) return '_';
  return '?';
}

function getPlantUmlUrl(text) {
  const deflated = zlib.deflateRawSync(Buffer.from(text, 'utf8'));
  const encoded = encode64(deflated.toString('binary'));
  return `https://www.plantuml.com/plantuml/png/~1${encoded}`;
}

async function downloadImage(url, filename) {
  return new Promise((resolve, reject) => {
    https.get(url, (res) => {
      if (res.statusCode !== 200) {
        reject(new Error(`Failed to download ${url}: ${res.statusCode}`));
        return;
      }
      const fileStream = fs.createWriteStream(filename);
      res.pipe(fileStream);
      fileStream.on('finish', () => {
        fileStream.close();
        resolve();
      });
    }).on('error', reject);
  });
}

const mdContent = fs.readFileSync('bab4_perancangan_sistem.md', 'utf8');

const regex = /```plantuml\n([\s\S]*?)```/g;
let match;
let index = 0;

const filenames = [
  '1_use_case_diagram.png',
  '2_activity_login.png',
  '3_activity_order.png',
  '4_activity_payment.png',
  '5_activity_void.png',
  '6_activity_report.png',
  '7_sequence_order.png',
  '8_sequence_payment.png',
  '9_sequence_void.png',
  '10_class_diagram.png'
];

async function main() {
  const outputDir = path.join(__dirname, 'diagrams_output');
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir);
  }

  while ((match = regex.exec(mdContent)) !== null) {
    if (index === 9) { // Only update class diagram
      const code = match[1];
      const url = getPlantUmlUrl(code);
      const filename = path.join(outputDir, filenames[index]);
      console.log(`Downloading ${filenames[index]}...`);
      try {
        await downloadImage(url, filename);
        console.log(`Saved ${filename}`);
      } catch (e) {
        console.error(`Error downloading ${filenames[index]}: ${e.message}`);
      }
    }
    index++;
  }
}

main();
