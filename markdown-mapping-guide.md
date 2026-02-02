# Guía de Mapeo: Markdown AI → Plataformas de Mensajería

Este documento complementa la comparativa técnica con **ejemplos prácticos** de cómo convertir el output típico de una IA (ChatGPT, Claude, Gemini) a cada plataforma.

---

## Escenario Real: Respuesta de ChatGPT

Imagina que una IA genera esta respuesta:

```markdown
# Resumen Ejecutivo

La **Q4 2025** mostró un crecimiento del **23%** en ingresos. Principales hallazgos:

- Revenue: $45.2M (_+23% YoY_)
- EBITDA: $12.1M (~27% margin~)
- Clientes activos: `1,247` (+15%)

> "El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en [nuestro dashboard](https://example.com/q4-2025).

## Próximos pasos

1. Expandir a LATAM
2. Contratar ~~10~~ **15 personas** en Q1
3. Lanzar producto ||secreto|| en marzo

```php
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

| Métrica | Q3 | Q4 | Cambio |
|---------|----|----|--------|
| Revenue | 36.7M | 45.2M | +23% |
| EBITDA  | 9.5M  | 12.1M | +27% |
```

---

## Conversión por Plataforma

### 1. WhatsApp

**Sintaxis convertida**:
```
*Resumen Ejecutivo*

La *Q4 2025* mostró un crecimiento del *23%* en ingresos. Principales hallazgos:

- Revenue: $45.2M (_+23% YoY_)
- EBITDA: $12.1M (27% margin)
- Clientes activos: ```1,247``` (+15%)

> "El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en nuestro dashboard (https://example.com/q4-2025).

*Próximos pasos*

1. Expandir a LATAM
2. Contratar 15 personas en Q1
3. Lanzar producto secreto en marzo

```
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

*Métricas comparativas:*
• Revenue | Q3: 36.7M | Q4: 45.2M | Cambio: +23%
  - EBITDA | Q3: 9.5M | Q4: 12.1M | Cambio: +27%
```

**Cambios aplicados**:
- Headers `#` → `*Negrita*` (no hay headers en WA)
- `**bold**` → `*bold*` (un solo asterisco)
- `~~strike~~` → texto plano (quitamos tachado en "10")
- `||spoiler||` → texto plano (no existe spoiler)
- Links `[texto](url)` → `texto (url)`
- Tabla → Convertida a lista con bullets
- Triple backtick para código se mantiene

---

### 2. Telegram (HTML mode)

**Sintaxis convertida**:
```html
<b>Resumen Ejecutivo</b>

La <b>Q4 2025</b> mostró un crecimiento del <b>23%</b> en ingresos. Principales hallazgos:

- Revenue: $45.2M (<i>+23% YoY</i>)
- EBITDA: $12.1M (<s>27% margin</s>)
- Clientes activos: <code>1,247</code> (+15%)

<blockquote>"El crecimiento fue impulsado por expansión geográfica" - CFO</blockquote>

Más detalles en <a href="https://example.com/q4-2025">nuestro dashboard</a>.

<b>Próximos pasos</b>

1. Expandir a LATAM
2. Contratar <s>10</s> <b>15 personas</b> en Q1
3. Lanzar producto <tg-spoiler>secreto</tg-spoiler> en marzo

<pre><code class="language-php">function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}</code></pre>

<b>Métricas</b>
<pre>
| Métrica | Q3    | Q4    | Cambio |
|---------|-------|-------|--------|
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
</pre>
```

**Cambios aplicados**:
- Headers `#` → `<b>` (no hay headers nativos en Bot API HTML)
- `**bold**` → `<b>bold</b>`
- `*italic*` → `<i>italic</i>`
- `~~strike~~` → `<s>strike</s>`
- `` `code` `` → `<code>code</code>`
- `> quote` → `<blockquote>quote</blockquote>`
- `[texto](url)` → `<a href="url">texto</a>`
- `||spoiler||` → `<tg-spoiler>spoiler</tg-spoiler>`
- ` ```php ` → `<pre><code class="language-php">`
- Tabla → Mantenida en `<pre>` para monospace

---

### 3. Telegram (MarkdownV2 mode)

**Sintaxis convertida** (con escapes):
```
*Resumen Ejecutivo*

La *Q4 2025* mostró un crecimiento del *23%* en ingresos\\. Principales hallazgos:

\\- Revenue: $45\\.2M \\(_\\+23% YoY_\\)
\\- EBITDA: $12\\.1M \\(~27% margin~\\)
\\- Clientes activos: `1,247` \\(\\+15%\\)

> "El crecimiento fue impulsado por expansión geográfica" \\- CFO

Más detalles en [nuestro dashboard](https://example\\.com/q4\\-2025)\\.

*Próximos pasos*

1\\. Expandir a LATAM
2\\. Contratar ~10~ *15 personas* en Q1
3\\. Lanzar producto ||secreto|| en marzo

```php
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

*Métricas*
```
| Métrica | Q3    | Q4    | Cambio |
|---------|-------|-------|--------|
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
```
```

**Caracteres escapados con `\`**:
- `.` → `\\.`
- `-` → `\\-` (cuando no es parte de lista o sintaxis)
- `(` `)` → `\\(` `\\)`
- `+` → `\\+`

**Nota**: MarkdownV2 es muy estricto. Si falta un escape, el mensaje falla. Por eso **HTML es más recomendable** para bots de Telegram.

---

### 4. Slack (mrkdwn)

**Sintaxis convertida**:
```
*Resumen Ejecutivo*

La *Q4 2025* mostró un crecimiento del *23%* en ingresos. Principales hallazgos:

• Revenue: $45.2M (_+23% YoY_)
• EBITDA: $12.1M (~27% margin~)
• Clientes activos: `1,247` (+15%)

> "El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en <https://example.com/q4-2025|nuestro dashboard>.

*Próximos pasos*

1. Expandir a LATAM
2. Contratar ~10~ *15 personas* en Q1
3. Lanzar producto secreto en marzo

```
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

*Métricas:*
```
| Métrica | Q3    | Q4    | Cambio |
|---------|-------|-------|--------|
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
```
```

**Cambios aplicados**:
- Headers `#` → `*Negrita*` (no hay headers)
- `**bold**` → `*bold*` (un solo asterisco)
- Links `[texto](url)` → `<url|texto>` ⚠️ **Sintaxis muy diferente**
- `||spoiler||` → texto plano (no existe)
- Listas: usar bullets manualmente (• o -)
- Saltos de línea: `\n` en el JSON payload

---

### 5. Discord

**Sintaxis convertida** (prácticamente sin cambios):
```markdown
# Resumen Ejecutivo

La **Q4 2025** mostró un crecimiento del **23%** en ingresos. Principales hallazgos:

- Revenue: $45.2M (*+23% YoY*)
- EBITDA: $12.1M (~~27% margin~~)
- Clientes activos: `1,247` (+15%)

> "El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en [nuestro dashboard](https://example.com/q4-2025).

## Próximos pasos

1. Expandir a LATAM
2. Contratar ~~10~~ **15 personas** en Q1
3. Lanzar producto ||secreto|| en marzo

```php
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

**Métricas:**
```
| Métrica | Q3    | Q4    | Cambio |
|---------|-------|-------|--------|
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
```
```

**Cambios aplicados**:
- ✅ **Casi ninguno** — Discord soporta Markdown real
- `||spoiler||` se mantiene (es extensión de Discord)
- Headers `#` funcionan
- Tabla se puede mostrar en bloque de código con ` ``` `

---

### 6. Microsoft Teams

**Sintaxis convertida**:
```markdown
**Resumen Ejecutivo**

La **Q4 2025** mostró un crecimiento del **23%** en ingresos. Principales hallazgos:

- Revenue: $45.2M (_+23% YoY_)
- EBITDA: $12.1M (~27% margin~)
- Clientes activos: `1,247` (+15%)

> "El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en [nuestro dashboard](https://example.com/q4-2025).

## Próximos pasos

1. Expandir a LATAM
2. Contratar ~10~ **15 personas** en Q1
3. Lanzar producto secreto en marzo

```
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

**Métricas:**
```
| Métrica | Q3    | Q4    | Cambio |
|---------|-------|-------|--------|
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
```
```

**Cambios aplicados**:
- Header `#` → `##` (Teams usa nivel 2+)
- `~~strike~~` → `~strike~` (una sola tilde)
- `||spoiler||` → texto plano (no existe)
- Todo lo demás funciona igual

---

### 7. Signal

**Resultado final** (sin sintaxis, solo texto plano con formato visual):
```
Resumen Ejecutivo

La Q4 2025 mostró un crecimiento del 23% en ingresos. Principales hallazgos:

- Revenue: $45.2M (+23% YoY)
- EBITDA: $12.1M (27% margin)
- Clientes activos: 1,247 (+15%)

"El crecimiento fue impulsado por expansión geográfica" - CFO

Más detalles en nuestro dashboard: https://example.com/q4-2025

Próximos pasos

1. Expandir a LATAM
2. Contratar 10 15 personas en Q1
3. Lanzar producto [SPOILER] en marzo

function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}

Métricas:
| Métrica | Q3    | Q4    | Cambio |
| Revenue | 36.7M | 45.2M | +23%   |
| EBITDA  | 9.5M  | 12.1M | +27%   |
```

**Notas**:
- Signal **NO acepta sintaxis Markdown escrita**
- El usuario debe seleccionar texto manualmente y aplicar formato desde UI
- Para automatización (bots), envía texto plano
- Spoilers se marcan con texto `[SPOILER]` o se omiten

---

## Matriz de Decisión: ¿Qué Plataforma Usar para Qué Caso?

| Necesitas... | Mejor opción | Por qué |
|--------------|--------------|---------|
| **Markdown real sin conversión** | Discord, Mattermost, Rocket.Chat, Matrix | Soportan sintaxis estándar |
| **Máxima compatibilidad HTML** | Telegram (HTML mode) | API robusta, tags completos |
| **Simplicidad en WhatsApp Business** | Convertir a bullets + monospace | WA no soporta mucho formato |
| **Integración empresarial** | Slack, Teams | APIs maduras, webhooks |
| **Privacidad + formato básico** | Signal | Cifrado E2E, formato UI manual |
| **Links clickeables bonitos** | Telegram, Discord, Teams | Renderizado nativo |
| **Spoilers / contenido oculto** | Discord, Telegram | Únicos que lo soportan |
| **Tablas nativas** | Mattermost, Rocket.Chat | GFM tables |
| **Sin conversión (texto plano)** | Signal, iMessage | No aceptan markup escrito |

---

## Código de Ejemplo: Función de Mapeo Universal

```php
function mapMarkdownToPlatform(string $markdown, string $platform): string
{
    return match($platform) {
        'whatsapp' => convertToWhatsApp($markdown),
        'telegram_html' => convertToTelegramHTML($markdown),
        'telegram_md' => convertToTelegramMarkdownV2($markdown),
        'slack' => convertToSlackMrkdwn($markdown),
        'discord' => $markdown, // Casi sin cambios
        'teams' => convertToTeams($markdown),
        'signal', 'imessage' => stripAllFormatting($markdown),
        'mattermost', 'rocketchat', 'matrix' => $markdown, // MD estándar
        default => throw new \Exception("Platform not supported: {$platform}"),
    };
}
```

---

## Referencias Rápidas

### Prioridad de Escape

| Plataforma | Requiere escape | Caracteres |
|------------|-----------------|------------|
| Telegram MarkdownV2 | ⚠️ Muy estricto | `_ * [ ] ( ) ~ \` > # + - = \| { } . !` |
| Slack | ❌ No soporta | N/A |
| Discord | ✅ Opcional | `\ \` * _ ~ \| ||` |
| Rocket.Chat | ✅ Con parser Marked | Caracteres MD estándar |

### Longitud Máxima de Mensajes

| Plataforma | Límite de caracteres |
|------------|---------------------|
| WhatsApp | 65,536 (pero UX óptimo: <1,000) |
| Telegram | 4,096 por mensaje |
| Slack | ~40,000 (depende del plan) |
| Discord | 2,000 |
| Teams | ~28,000 |
| Signal | ~1,500 (UX recomendado) |

---

## Casos Edge: Problemas Comunes

### 1. Links con caracteres especiales en Rocket.Chat
**Problema**: `[Issue #123: Fix [bug]](url)` rompe el parser  
**Solución**: Usar parser "Marked" (v4.0.0+) y escapar `]` → `\]`

### 2. Telegram MarkdownV2: parsing fallido
**Problema**: `*bold.* text` falla (el `.` no está escapado)  
**Solución**: `*bold\\.* text` o usar HTML mode

### 3. WhatsApp monospace rompe formato
**Problema**: ` ```*bold*``` ` muestra `*bold*` literal  
**Solución**: No mezclar monospace con otros formatos en WA

### 4. Slack links truncados
**Problema**: `<https://very-long-url.com|Ver más>` se corta visualmente  
**Solución**: Acortar el label o usar URL shortener

---

## Checklist de Testing

Antes de lanzar tu librería, prueba estos casos en **cada plataforma**:

- [ ] Negrita simple: `**bold**`
- [ ] Cursiva simple: `*italic*`
- [ ] Combinación: `**_bold italic_**`
- [ ] Link con label: `[Google](https://google.com)`
- [ ] Link con caracteres especiales: `[Test [1]](url)`
- [ ] Código inline con backticks: `` `código` ``
- [ ] Bloque de código con lenguaje: ` ```php\necho "hi";\n``` `
- [ ] Lista mezclada (bullets + numerada)
- [ ] Tabla Markdown (si aplica)
- [ ] Blockquote multilínea
- [ ] Spoiler (Discord/Telegram)
- [ ] Emojis: 🚀 🔥 ✅
- [ ] Texto con más de 2,000 caracteres (chunking)
- [ ] Caracteres Unicode: é, ñ, 中文

---

**Última actualización**: Febrero 1, 2026  
**Mantenedor**: Tu paquete `chat-markdown`
