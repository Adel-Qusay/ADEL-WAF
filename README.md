# ADEL-WAF
PHP lightweight in-app Web Application Firewall.

![alt text](https://raw.githubusercontent.com/Adel-Qusay/ADEL-WAF/main/Screenshot.png)

Features / Protections Against:
- Cross-site scripting (XSS)
- SQL injection (SQLI)
- Remote file inclusion (RFI)
- Remote code execution (RCE)
- Local file inclusion (LFI)
- Denial of service (DOS)
- Web shells

How to use:

Configure your web server:

Apache: php_value auto_prepend_file "/path/to/AdelWAF.php"

Nginx: fastcgi_param PHP_VALUE "auto_prepend_file=/path/to/AdelWAF.php";

--------------------------------OR--------------------------------

Include this PHP file in every page you want to protect.

Note: Works with all PHP frameworks (Use it as a filter)

