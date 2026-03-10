import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'api_service.dart';

class TransactionsScreen extends StatefulWidget {
  @override
  _TransactionsScreenState createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  List<dynamic> _receitas = [];
  List<dynamic> _despesas = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchTransactions();
  }

  Future<void> _fetchTransactions() async {
    setState(() => _isLoading = true);
    try {
      final receitas = await ApiService.getReceitas();
      final despesas = await ApiService.getDespesas();
      
      if (mounted) {
        setState(() {
          _receitas = receitas;
          _despesas = despesas;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  String formatCurrency(dynamic value) {
    num parsedValue = value is String ? num.tryParse(value) ?? 0 : value;
    return NumberFormat.currency(locale: 'pt_BR', symbol: 'R\$').format(parsedValue);
  }

  Widget _buildList(List<dynamic> items, bool isReceita) {
    if (items.isEmpty) return Center(child: Text("Nenhuma transação encontrada."));
    
    return ListView.builder(
      itemCount: items.length,
      itemBuilder: (context, index) {
        final item = items[index];
        return ListTile(
          leading: CircleAvatar(
            backgroundColor: isReceita ? Colors.green[100] : Colors.red[100],
            child: Icon(
              isReceita ? Icons.arrow_upward : Icons.arrow_downward,
              color: isReceita ? Colors.green : Colors.red,
            ),
          ),
          title: Text(item['descricao'] ?? 'Sem descrição', style: TextStyle(fontWeight: FontWeight.bold)),
          subtitle: Text(item['categoria'] ?? 'Geral'),
          trailing: Text(
            formatCurrency(item['valor']),
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              color: isReceita ? Colors.green : Colors.red,
            ),
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: Text('Transações'),
          backgroundColor: Colors.blueAccent,
          foregroundColor: Colors.white,
          bottom: TabBar(
            labelColor: Colors.white,
            unselectedLabelColor: Colors.white70,
            indicatorColor: Colors.white,
            tabs: [
              Tab(text: 'Receitas'),
              Tab(text: 'Despesas'),
            ],
          ),
        ),
        body: _isLoading
            ? Center(child: CircularProgressIndicator())
            : TabBarView(
                children: [
                  _buildList(_receitas, true),
                  _buildList(_despesas, false),
                ],
              ),
      ),
    );
  }
}
