import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'api_service.dart';
import 'transactions_screen.dart';
import 'add_transaction_screen.dart';
import 'login_screen.dart';

class DashboardScreen extends StatefulWidget {
  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  Map<String, dynamic>? _summary;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchSummary();
  }

  Future<void> _fetchSummary() async {
    setState(() => _isLoading = true);
    try {
      final summary = await ApiService.getSummary();
      if (mounted) {
        setState(() {
          _summary = summary;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Erro ao carregar resumo.')));
      }
    }
  }

  void _logout() async {
    await ApiService.logout();
    if (mounted) {
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
    }
  }

  String formatCurrency(dynamic value) {
    if (value == null) return "R\$ 0,00";
    num parsedValue = value is String ? num.tryParse(value) ?? 0 : value;
    return NumberFormat.currency(locale: 'pt_BR', symbol: 'R\$').format(parsedValue);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: Text('Minhas Finanças', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
        actions: [
          IconButton(icon: Icon(Icons.refresh), onPressed: _fetchSummary),
          IconButton(icon: Icon(Icons.logout), onPressed: _logout),
        ],
      ),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator())
        : Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                _buildSummaryCard("Saldo Atual", _summary?['saldo'], Colors.blue),
                SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(child: _buildSummaryCard("Receitas", _summary?['receitas'], Colors.green, isSmall: true)),
                    SizedBox(width: 16),
                    Expanded(child: _buildSummaryCard("Despesas", _summary?['despesas'], Colors.red, isSmall: true)),
                  ],
                ),
                SizedBox(height: 32),
                Text('Acesso Rápido', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                SizedBox(height: 16),
                Expanded(
                  child: GridView.count(
                    crossAxisCount: 2,
                    crossAxisSpacing: 16,
                    mainAxisSpacing: 16,
                    children: [
                      _buildMenuCard(Icons.list, 'Transações', Colors.purple, () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => TransactionsScreen())).then((_) => _fetchSummary());
                      }),
                      _buildMenuCard(Icons.add_circle, 'Nova Receita', Colors.green, () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => AddTransactionScreen(tipo: 'receita'))).then((_) => _fetchSummary());
                      }),
                      _buildMenuCard(Icons.remove_circle, 'Nova Despesa', Colors.red, () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => AddTransactionScreen(tipo: 'despesa'))).then((_) => _fetchSummary());
                      }),
                    ],
                  ),
                ),
              ],
            ),
          ),
    );
  }

  Widget _buildSummaryCard(String title, dynamic value, MaterialColor color, {bool isSmall = false}) {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Container(
        padding: EdgeInsets.all(isSmall ? 16 : 24),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          gradient: LinearGradient(colors: [color.shade400, color.shade700], begin: Alignment.topLeft, end: Alignment.bottomRight),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title, style: TextStyle(color: Colors.white70, fontSize: isSmall ? 14 : 18)),
            SizedBox(height: 8),
            Text(formatCurrency(value), style: TextStyle(color: Colors.white, fontSize: isSmall ? 20 : 32, fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuCard(IconData icon, String title, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 48, color: color),
            SizedBox(height: 8),
            Text(title, style: GoogleFonts.poppins(fontWeight: FontWeight.w600)),
          ],
        ),
      ),
    );
  }
}
