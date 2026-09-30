import { cp, mkdir, rm, readdir } from 'node:fs/promises';
import { resolve } from 'node:path';

const publicDir = resolve('public');
const outDir = resolve('public/static-deploy');

// Only index.php and server configs are excluded:
// If index.php lands in the output directory, a request for / resolves to it on disk
// and Vercel returns the PHP source as plain text instead of executing it.
// Every application route is handled by the serverless function instead.
const excludedEntries = new Set([
    'index.php',
    'static-deploy',
    '.htaccess',
    'web.config',
    'storage'
]);

await rm(outDir, { recursive: true, force: true });
await mkdir(outDir, { recursive: true });

const entries = await readdir(publicDir);

for (const entry of entries) {
    if (excludedEntries.has(entry) || entry.startsWith('.')) {
        continue;
    }

    const source = resolve(publicDir, entry);
    await cp(source, resolve(outDir, entry), { recursive: true });
    console.log(`[vercel-assets] staged public/${entry}`);
}
