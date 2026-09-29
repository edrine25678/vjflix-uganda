import { cp, mkdir, rm } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { resolve } from 'node:path';

const publicDir = resolve('public');
const outDir = resolve('public/static-deploy');

// Only these ship as static files. public/index.php is deliberately excluded:
// if it lands in the output directory, a request for / resolves to it on disk
// and Vercel returns the PHP source as plain text instead of executing it.
// Every application route is handled by the serverless function instead.
const staticEntries = ['build', 'img', 'favicon.ico', 'robots.txt'];

await rm(outDir, { recursive: true, force: true });
await mkdir(outDir, { recursive: true });

for (const entry of staticEntries) {
    const source = resolve(publicDir, entry);

    if (!existsSync(source)) {
        console.warn(`[vercel-assets] skipped missing public/${entry}`);
        continue;
    }

    await cp(source, resolve(outDir, entry), { recursive: true });
    console.log(`[vercel-assets] staged public/${entry}`);
}
