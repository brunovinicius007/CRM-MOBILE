import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Troque pelo seu IP local se usar o celular físico, ou 10.0.2.2 para emulador Android
  static const String baseUrl = 'http://10.0.2.2/CRM-MOBILE/api';

  // Salva o cookie de sessão do PHP (PHPSESSID) para o login não cair
  static String? _cookie;

  static Map<String, String> _getHeaders() {
    final headers = {'Content-Type': 'application/json'};
    if (_cookie != null) headers['Cookie'] = _cookie!;
    return headers;
  }

  static void _updateCookie(http.Response response) {
    String? rawCookie = response.headers['set-cookie'];
    if (rawCookie != null) {
      int index = rawCookie.indexOf(';');
      _cookie = (index == -1) ? rawCookie : rawCookie.substring(0, index);
    }
  }

  static Future<Map<String, dynamic>> login(String email, String senha) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login.php'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'senha': senha}),
    );

    _updateCookie(response);
    final data = jsonDecode(response.body);

    if (response.statusCode == 200) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool('isLoggedIn', true);
      await prefs.setString('userEmail', email);
      if (_cookie != null) await prefs.setString('sessionCookie', _cookie!);
    }

    return data;
  }

  static Future<Map<String, dynamic>?> getSummary() async {
    await _loadCookie();
    final response = await http.get(Uri.parse('$baseUrl/get_summary.php'), headers: _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<List<dynamic>> getReceitas() async {
    await _loadCookie();
    final response = await http.get(Uri.parse('$baseUrl/list_receitas.php'), headers: _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return [];
  }

  static Future<List<dynamic>> getDespesas() async {
    await _loadCookie();
    final response = await http.get(Uri.parse('$baseUrl/list_despesas.php'), headers: _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return [];
  }

  static Future<bool> addTransaction(String tipo, Map<String, dynamic> data) async {
    await _loadCookie();
    final endpoint = tipo == 'receita' ? '/create_receita.php' : '/create_despesa.php';
    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: _getHeaders(),
      body: jsonEncode(data),
    );
    return response.statusCode == 200;
  }

  static Future<void> _loadCookie() async {
    if (_cookie == null) {
      final prefs = await SharedPreferences.getInstance();
      _cookie = prefs.getString('sessionCookie');
    }
  }

  static Future<void> logout() async {
    await _loadCookie();
    await http.get(Uri.parse('$baseUrl/logout.php'), headers: _getHeaders());
    _cookie = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }
}
