import React, { useState, useEffect } from 'react'
import { Head, useForm } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'
import DatePicker from 'react-datepicker'
import "react-datepicker/dist/react-datepicker.css"

interface Pessoa {
    pes_id: number;
    pes_nome: string;
}

interface Unidade {
    unid_id: number;
    unid_nome: string;
    unid_sigla: string;
}

interface Lotacao {
    lot_id: number;
    pes_id: number;
    unid_id: number;
    lot_data_lotacao: string;
    lot_data_remocao: string | null;
    lot_portaria: string | null;
    pessoa: Pessoa;
    unidade: Unidade;
}

interface EditProps extends PageProps {
    lotacao: Lotacao;
    pessoas: Pessoa[];
    unidades: Unidade[];
}

export default function Edit({ lotacao, pessoas, unidades, errors }: EditProps & { errors: Record<string, string> }) {
    const { data, setData, put, processing } = useForm({
        pes_id: lotacao.pes_id.toString(),
        unid_id: lotacao.unid_id.toString(),
        lot_data_lotacao: lotacao.lot_data_lotacao,
        lot_data_remocao: lotacao.lot_data_remocao,
        lot_portaria: lotacao.lot_portaria || '',
    });

    const [dataLotacao, setDataLotacao] = useState<Date | null>(null);
    const [dataRemocao, setDataRemocao] = useState<Date | null>(null);

    // Inicializar as datas ao carregar o componente
    useEffect(() => {
        if (lotacao.lot_data_lotacao) {
            setDataLotacao(new Date(lotacao.lot_data_lotacao));
        }
        if (lotacao.lot_data_remocao) {
            setDataRemocao(new Date(lotacao.lot_data_remocao));
        }
    }, [lotacao]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('lotacao.update', lotacao.lot_id));
    };

    const handleDataLotacaoChange = (date: Date | null) => {
        setDataLotacao(date);
        setData('lot_data_lotacao', date ? date.toISOString().split('T')[0] : '');
    };

    const handleDataRemocaoChange = (date: Date | null) => {
        setDataRemocao(date);
        setData('lot_data_remocao', date ? date.toISOString().split('T')[0] : null);
    };

    return (
        <>
            <Head title="Editar Lotação" />

            <div className="container mx-auto py-8">
                <div className="max-w-2xl mx-auto">
                    <h1 className="text-2xl font-semibold mb-6">Editar Lotação</h1>

                    <form onSubmit={handleSubmit}>
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_id">
                                    Servidor
                                </label>
                                <select
                                    id="pes_id"
                                    value={data.pes_id}
                                    onChange={e => setData('pes_id', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="">Selecione um servidor</option>
                                    {pessoas.map((pessoa) => (
                                        <option key={pessoa.pes_id} value={pessoa.pes_id}>
                                            {pessoa.pes_nome}
                                        </option>
                                    ))}
                                </select>
                                {errors.pes_id && <p className="text-red-500 text-xs mt-1">{errors.pes_id}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="unid_id">
                                    Unidade
                                </label>
                                <select
                                    id="unid_id"
                                    value={data.unid_id}
                                    onChange={e => setData('unid_id', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="">Selecione uma unidade</option>
                                    {unidades.map((unidade) => (
                                        <option key={unidade.unid_id} value={unidade.unid_id}>
                                            {unidade.unid_nome} ({unidade.unid_sigla})
                                        </option>
                                    ))}
                                </select>
                                {errors.unid_id && <p className="text-red-500 text-xs mt-1">{errors.unid_id}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="lot_data_lotacao">
                                    Data de Lotação
                                </label>
                                <DatePicker
                                    id="lot_data_lotacao"
                                    selected={dataLotacao}
                                    onChange={handleDataLotacaoChange}
                                    dateFormat="dd/MM/yyyy"
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholderText="DD/MM/AAAA"
                                    required
                                />
                                {errors.lot_data_lotacao && <p className="text-red-500 text-xs mt-1">{errors.lot_data_lotacao}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="lot_data_remocao">
                                    Data de Remoção
                                </label>
                                <DatePicker
                                    id="lot_data_remocao"
                                    selected={dataRemocao}
                                    onChange={handleDataRemocaoChange}
                                    dateFormat="dd/MM/yyyy"
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholderText="DD/MM/AAAA"
                                    isClearable
                                    minDate={dataLotacao || undefined}
                                />
                                {errors.lot_data_remocao && <p className="text-red-500 text-xs mt-1">{errors.lot_data_remocao}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="lot_portaria">
                                    Portaria
                                </label>
                                <input
                                    type="text"
                                    id="lot_portaria"
                                    value={data.lot_portaria || ''}
                                    onChange={e => setData('lot_portaria', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                {errors.lot_portaria && <p className="text-red-500 text-xs mt-1">{errors.lot_portaria}</p>}
                            </div>

                            <div className="flex items-center justify-end mt-6">
                                <button
                                    type="button"
                                    onClick={() => window.history.back()}
                                    className="mr-4 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    {processing ? 'Salvando...' : 'Salvar'}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

