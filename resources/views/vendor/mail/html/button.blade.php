@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <a href="{{ $url }}"
                class="button"
                target="_blank"
                style="
                    background-color: #001F3F;     /* Bleu de nuit */
                    border: 1px solid #FFD700;     /* blanc or */
                    color: #FFD700;                /* Texte blanc or */
                    display: inline-block;
                    padding: 10px 18px;
                    font-size: 16px;
                    border-radius: 4px;
                    text-decoration: none;
                ">
                {{ $slot }}
            </a>
        </td>
    </tr>
    </table>
</td>
</tr>
</table>
