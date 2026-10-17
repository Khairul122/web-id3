import fs from 'node:fs/promises';
import path from 'node:path';
import puppeteer from 'puppeteer-core';
import sharp from 'sharp';
const dir=path.resolve(import.meta.dirname,'..');
const browser=await puppeteer.launch({executablePath:'C:/Program Files/Google/Chrome/Application/chrome.exe',headless:true,args:['--no-sandbox']});
try{
 const page=await browser.newPage();
 await page.setViewport({width:900,height:1450,deviceScaleFactor:1});
 await page.goto('http://web-id3.test/',{waitUntil:'networkidle0'});
 await page.evaluate(()=>document.fonts.ready);
 await page.screenshot({path:path.join(dir,'assets/site-mobile.png')});
 for(const view of ['form','review','detail']){
  await page.setContent(await fs.readFile(path.join(dir,'capture',view+'.html'),'utf8'),{waitUntil:'networkidle0'});
  await page.evaluate(()=>document.fonts.ready);
  await page.screenshot({path:path.join(dir,'assets',view+'.png'),fullPage:true});
  const boxes=await page.evaluate(()=>Object.fromEntries([...document.querySelectorAll('h1,h2,input,select,button,.result-head,.path,.notice')].map((el,i)=>{const b=el.getBoundingClientRect();return [el.id||el.name||el.className||el.tagName+i,{x:b.x,y:b.y,width:b.width,height:b.height,text:(el.innerText||el.value||'').slice(0,130)}]})));
  await fs.writeFile(path.join(dir,'capture',view+'-boxes.json'),JSON.stringify(boxes,null,2));
 }
 await sharp(path.join(dir,'assets/detail.png')).resize({width:540}).toFile(path.join(dir,'capture/result-inspect.png'));
}finally{await browser.close()}
console.log('Captured real views and public homepage at 900px with anonymous example.');
