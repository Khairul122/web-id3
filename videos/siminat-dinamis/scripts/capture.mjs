import fs from 'node:fs/promises';
import path from 'node:path';
import puppeteer from 'puppeteer-core';
import sharp from 'sharp';
const dir=path.resolve(import.meta.dirname,'..');
const supportingCaptures=path.join(dir,'capture','screenshots');
await fs.mkdir(supportingCaptures,{recursive:true});
const root=path.resolve(dir,'../..');
const css=(await Promise.all(['tokens','admin'].map(name=>fs.readFile(path.join(root,'assets/css',name+'.css'),'utf8')))).join('\n');
const font=await fs.readFile(path.join(root,'assets/vendor/fonts/inter-latin.woff2'));
const browser=await puppeteer.launch({executablePath:'C:/Program Files/Google/Chrome/Application/chrome.exe',headless:true,args:['--no-sandbox']});
try{
 const page=await browser.newPage();
 await page.setViewport({width:900,height:1450,deviceScaleFactor:1});
 await page.goto('http://web-id3.test/',{waitUntil:'networkidle0'});
 await page.evaluate(()=>document.fonts.ready);
 await page.screenshot({path:path.join(supportingCaptures,'site-mobile.png')});
 for(const view of ['form','review','detail']){
  await page.goto('about:blank');
  const html=(await fs.readFile(path.join(dir,'capture',view+'.html'),'utf8')).replace(/<base[^>]+>/g,'').replace(/<link[^>]+>/g,'').replace('</head>',`<style>${css}\n@font-face{font-family:Inter;src:url(data:font/woff2;base64,${font.toString('base64')}) format('woff2');font-weight:100 900}</style></head>`);
  await page.setContent(html,{waitUntil:'domcontentloaded'});
  await page.evaluate(()=>document.fonts.ready);
  const screenshotDir=view==='review'?supportingCaptures:path.join(dir,'assets');
  await page.screenshot({path:path.join(screenshotDir,view+'.png'),fullPage:true});
  const boxes=await page.evaluate(()=>Object.fromEntries([...document.querySelectorAll('h1,h2,input,select,button,.result-head,.path,.notice')].map((el,i)=>{const b=el.getBoundingClientRect();return [el.id||el.name||el.className||el.tagName+i,{x:b.x,y:b.y,width:b.width,height:b.height,text:(el.innerText||el.value||'').slice(0,130)}]})));
  await fs.writeFile(path.join(dir,'capture',view+'-boxes.json'),JSON.stringify(boxes,null,2));
 }
 await sharp(path.join(dir,'assets/detail.png')).resize({width:540}).toFile(path.join(dir,'capture/result-inspect.png'));
}finally{await browser.close()}
console.log('Captured real views and public homepage at 900px with anonymous example.');
